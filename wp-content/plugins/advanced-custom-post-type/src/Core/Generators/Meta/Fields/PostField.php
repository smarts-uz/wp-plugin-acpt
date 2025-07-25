<?php

namespace ACPT\Core\Generators\Meta\Fields;

use ACPT\Constants\MetaTypes;
use ACPT\Constants\RelationCostants;
use ACPT\Constants\Relationships;
use ACPT\Core\Helper\Strings;
use ACPT\Core\Models\Meta\MetaFieldModel;
use ACPT\Core\Models\Meta\MetaFieldRelationshipModel;
use ACPT\Core\Repository\OptionPageRepository;
use ACPT\Utils\PHP\Arrays;
use ACPT\Utils\PHP\Url;
use ACPT\Utils\Wordpress\Translator;
use ACPT\Utils\Wordpress\Users;

class PostField extends AbstractField
{
	/**
	 * @return mixed|string
	 * @throws \Exception
	 */
	public function render()
	{
        $this->enqueueAssets();

		if(empty($this->metaField->getRelations())){
			return '<p data-message-id="'.$this->metaField->getId().'" class="update-nag notice notice-warning inline no-records">'.Translator::translate("No relation set on this field.").'</p>';
		}

		$relation = $this->metaField->getRelations()[0];
		$errors = $this->checkIfTheFieldCanBeRendered($relation);

		if(!empty($errors)){
			return '<p data-message-id="'.$this->metaField->getId().'" class="update-nag notice notice-warning inline no-records">'.Translator::translate($errors).'</p>';
		}

		if($this->isChild() or $this->isNestedInABlock()){
			$field = '<input type="hidden" name="'. esc_attr($this->getIdName()).'[type]" value="'.MetaFieldModel::POST_TYPE.'">';
			$field .= '<input type="hidden" name="'. esc_attr($this->getIdName()).'[original_name]" value="'.$this->metaField->getName().'">';
		} else {
			$field = '<input type="hidden" name="'. esc_attr($this->getIdName()).'_type" value="'.MetaFieldModel::POST_TYPE.'">';
		}

		$fieldName = esc_attr($this->getIdName());
		$isMulti = $this->isMulti($relation->getRelationship()) ? 'multiple' : '';
		$options = $this->getOptions($relation);
		//$unavailableValues = $this->unavailableValues($relation, $fieldName);
		$defaultValue = $this->getDefaultValue();
		$layout = $this->getAdvancedOption('layout');

		$field .= $this->inversedHiddenInputs($relation);
		$field .= $this->renderRelationFieldSelector($isMulti, $fieldName, $options, $defaultValue, $layout);

		return $this->renderField($field);
	}

	/**
	 * @param MetaFieldRelationshipModel $relationshipModel
	 *
	 * @return string|null
	 */
	private function checkIfTheFieldCanBeRendered(MetaFieldRelationshipModel $relationshipModel)
	{
        $pagenow = Url::pagenow();

		$from = $relationshipModel->from();

		switch ($pagenow){

			case "user-edit.php":
				if($from->getType() !== MetaTypes::USER){
					return 'From entity is not an user';
				}
				break;

			case "admin.php":
				if($from->getType() !== MetaTypes::OPTION_PAGE){
					return 'From entity is not an option page';
				}
				break;

			case "post.php":
				$postType = isset($_GET['post']) ? get_post_type($_GET['post']) : 'post';

				if($from->getType() !== MetaTypes::CUSTOM_POST_TYPE){
					return 'From entity is not a custom post type';
				}

				if($from->getValue() !== $postType){
					return 'From entity is not valid';
				}

				break;

			case "post-new.php":
				$postType = $_GET['post_type'] ?? 'post';

				if($from->getType() !== MetaTypes::CUSTOM_POST_TYPE){
					return 'From entity is not a custom post type';
				}

				if($from->getValue() !== $postType){
					return 'From entity is not valid';
				}

				break;

			case "edit-tags.php":
			case "term.php":
				$taxonomy = $_GET['taxonomy'];

				if($from->getType() !== MetaTypes::TAXONOMY){
					return 'From entity is not a taxonomy';
				}

				if($from->getValue() !== $taxonomy){
					return 'From entity is not valid';
				}

				break;
		}

		return null;
	}

	/**
	 * @param MetaFieldRelationshipModel $relationshipModel
	 *
	 * @return string
	 */
	private function inversedHiddenInputs(MetaFieldRelationshipModel $relationshipModel)
	{
		$field = '';

		if($relationshipModel->getInversedBy() !== null){
			$inversedBy = $relationshipModel->getInversedBy();
			$inversedIdName = $this->getInversedIdName($inversedBy->getBox()->getName(), $inversedBy->getName());
			$defaultValues = $this->getDefaultValue();
			$defaultValues = (is_array($defaultValues)) ? implode(',', $defaultValues) : $defaultValues;

			$field .= '<input type="hidden" name="meta_fields[]" value="'. esc_attr($inversedIdName).RelationCostants::RELATION_KEY.'">';
			$field .= '<input type="hidden" id="inversedBy" name="'. esc_attr($inversedIdName).RelationCostants::RELATION_KEY.'" value="'.esc_attr($defaultValues).'">';
			$field .= '<input type="hidden" id="inversedBy_original_values" name="'. esc_attr($inversedIdName).RelationCostants::RELATION_KEY.'_original_values" value="'.esc_attr($defaultValues).'">';
		}

		return $field;
	}

	/**
	 * @param $box
	 * @param $field
	 *
	 * @return string
	 */
	private function getInversedIdName($box, $field)
	{
		return Strings::toDBFormat($box) . RelationCostants::SEPARATOR . Strings::toDBFormat($field);
	}

	/**
	 * @param string $relationship
	 *
	 * @return bool
	 */
	private function isMulti($relationship)
	{
		return (
			$relationship === Relationships::ONE_TO_MANY_UNI or
			$relationship === Relationships::ONE_TO_MANY_BI or
			$relationship === Relationships::MANY_TO_MANY_UNI or
			$relationship === Relationships::MANY_TO_MANY_BI
		);
	}

	/**
	 * @param MetaFieldRelationshipModel $relationshipModel
	 *
	 * @return array
	 * @throws \Exception
	 */
	private function getOptions(MetaFieldRelationshipModel $relationshipModel)
	{
		$to = $relationshipModel->to();

		switch($to->getType()){

			case MetaTypes::MEDIA:
			case MetaTypes::CUSTOM_POST_TYPE:

				$posts = [];
				$data = get_posts([
					'exclude'        => [get_the_id()],
					'post_type'      => $to->getValue(),
					'posts_per_page' => -1,
				]);

				foreach ($data as $post){
					$posts[] = [
						'value' => $post->ID,
						'label' => $post->post_title,
					];
				}

				return $posts;

			case MetaTypes::TAXONOMY:

				$terms = [];
				$data = $categoryIds = get_terms([
					'taxonomy'   => $to->getValue(),
					'hide_empty' => false,
				]);

				foreach ($data as $term){
					$terms[] = [
						'value' => $term->term_id,
						'label' => $term->name,
					];
				}

				return $terms;

			case MetaTypes::OPTION_PAGE:

				$pages = [];
				$data = OptionPageRepository::get([]);

				foreach ($data as $page){
					$pages[] = [
						'label' => $page->getMenuTitle(),
						'value' => $page->getId(),
					];

					foreach ($page->getChildren() as $child){
						$pages[] = [
							'label' => $child->getMenuTitle(),
							'value' => $child->getId(),
						];
					}
				}

				return $pages;

			case MetaTypes::USER:

				$users = [];

				foreach(Users::getList() as $id => $user){
					$users[] = [
						'label' => $user,
						'value' => $id
					];
				}

				return $users;
		}

		return [];
	}

	/**
	 * @param string $isMulti
	 * @param string $fieldName
	 * @param array $options
	 * @param null $defaultValue
	 * @param null $layout
	 *
	 * @return string
	 */
	private function renderRelationFieldSelector( string $isMulti, string $fieldName, array $options, $defaultValue = null, $layout = null)
	{
		$id = "relational_".Strings::generateRandomId();
		$defaultValueAsString = (is_array($defaultValue)) ? implode(",", $defaultValue) : '';

		$return = '<div class="acpt-relation-field-selector">';

		if($this->isChild() or $this->isNestedInABlock()){
			$return .= '<input type="hidden" '.$this->appendDataValidateAndLogicAttributes().' id="values_'.$id.'" name="'. esc_attr($fieldName).'[value]" value="'.$defaultValueAsString.'"/>';
		} else {
			$return .= '<input type="hidden" '.$this->appendDataValidateAndLogicAttributes().' id="values_'.$id.'" name="'. esc_attr($fieldName).'" value="'.$defaultValueAsString.'"/>';
		}

		// options
		$return .= '<div class="options">';
		$return .= '<div class="search">';
		$return .= '<input type="text" id="search_'.$id.'" class="acpt-form-control" placeholder="search items" />';
		$return .= '</div>';
		$return .= '<div class="values" data-max="'.(!$isMulti ? 1 : '').'" id="options_'.$id.'">';

		foreach ($options as $option){
			$cssClass = (is_array($defaultValue) and in_array($option['value'], $defaultValue)) ? 'hidden selected' : '';

			$return .= '
				<div 
					id="'.$option['value'].'" 
					class="value '.$cssClass.'" 
					data-value="'.$option['value'].'"
				>
					'.$this->renderLabel($option['label'], $option['value'], $layout).'
				</div>
			';
		}

		$return .= '</div>';

		// Add post
        $return .= $this->addPostLink($id);
		$return .= '</div>';

		// selected items
		$return .= '<div class="selected-items">';
		$return .= '<div class="title" id="title_'.$id.'">';
		$return .= '<h4>'.Translator::translate('Selected items').'</h4>';

		if(is_array($defaultValue) and !empty($defaultValue)){
			$return .= '<a href="#" id="'.$id.'" class="delete-all">'.Translator::translate('Delete all').'</a>';
		}

		$return .= '</div>';
		$return .= '<div class="values" id="selected_items_'.$id.'">';

		if(is_array($defaultValue)){
			foreach ($defaultValue as $value){

				$filter = array_filter($options, function($item) use ($value) {
					return $item['value'] == $value;
				});

				if(!empty($filter)){
                    $filter = Arrays::reindex($filter);

                    $return .= '
                        <div class="value" id="'.$value.'" data-value="'.$value.'">
                            <span class="placeholder">'.$this->renderLabel($filter[0]['label'], $value, $layout).'</span>
                            <a class="delete" href="#">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24">
                                    <path d="M5 20a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8h2V6h-4V4a2 2 0 0 0-2-2H9a2 2 0 0 0-2 2v2H3v2h2zM9 4h6v2H9zM8 8h9v12H7V8z"></path><path d="M9 10h2v8H9zm4 0h2v8h-2z"></path>
                                </svg>
                            </a>
                        </div>
                    ';
                }
			}
		}

		$return .= '</div>';
		$return .= '</div>';
		$return .= '</div>';

		return $return;
	}

    /**
     * @param $id
     * @return string
     */
	private function addPostLink($id)
    {
        $return = '';
        $relation = $this->metaField->getRelations()[0];
        $to = $relation->to();

        if ($to->getType() === MetaTypes::CUSTOM_POST_TYPE and current_user_can('publish_posts')) {
            $return .= '<div class="add-post-wrapper">';
            $return .= '<a class="acpt-modal-link add-post" href="#acpt-create-post-'.$id.'" rel="modal:open">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" style="fill: currentColor;transform: ;msFilter:;"><path d="M13 7h-2v4H7v2h4v4h2v-4h4v-2h-4z"></path><path d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2zm0 18c-4.411 0-8-3.589-8-8s3.589-8 8-8 8 3.589 8 8-3.589 8-8 8z"></path></svg>
                <span>'.Translator::translate("Add new post a link it").'</span>
            </a>';

            // modal
            $return .= '<div id="acpt-create-post-'.$id.'" class="acpt-create-post-modal modal">';
            $return .= '<h3>'.Translator::translate("Create a new post").'</h3>';
            $return .= '<input type="text" class="post-name acpt-form-control" style="margin-bottom: 5px;" placeholder="Write the post title here..." />';
            $return .= '<button disabled class="acpt-modal-link button button-primary" 
                            data-field-id="'.$this->metaField->getId().'" 
                            data-entity-type="'.$to->getType().'" 
                            data-entity-value="'.$to->getValue().'" 
                            data-entity-id="'.$this->find.'">
                                '.Translator::translate("Create").'
                            </button>';
            $return .= '</div>';

            $return .= '</div>';
        }

        return $return;
    }

	/**
	 * @param $label
	 * @param $value
	 * @param null $layout
	 *
	 * @return mixed
	 */
	private function renderLabel($label, $value, $layout = null)
	{
		if($layout === 'image'){
			$relation = $this->metaField->getRelations()[0];

			switch ($relation->to()->getType()){

				case MetaTypes::CUSTOM_POST_TYPE:
				case MetaTypes::MEDIA:
					$thumbnailUrl = get_the_post_thumbnail_url($value);

					if(empty($thumbnailUrl)){
						$thumbnailUrl = "https://placehold.co/40x40";
					}

					return $this->renderItemWithThumbnail($thumbnailUrl, $label);;

				case MetaTypes::USER:
					$thumbnailUrl = get_avatar_url($value);

					if(empty($thumbnailUrl)){
						$thumbnailUrl = "https://placehold.co/40x40";
					}

					return $this->renderItemWithThumbnail($thumbnailUrl, $label);

				default:
					return $label;
			}
		}

		return $label;
	}

	/**
	 * @param $thumbnailUrl
	 * @param $label
	 *
	 * @return string
	 */
	private function renderItemWithThumbnail($thumbnailUrl, $label)
	{
		$labelToRender = '<div class="selectize-item">';
		$labelToRender .= '<div class="selectize-thumbnail">';
		$labelToRender .= '<img src="'.$thumbnailUrl.'" width="40" height="40" alt="'.$label.'">';
		$labelToRender .= '</div>';
		$labelToRender .= '<div class="selectize-label">'.$label.'</div>';
		$labelToRender .= '</div>';

		return $labelToRender;
	}

    /**
     * Enqueue assets
     */
    private function enqueueAssets()
    {
        wp_enqueue_script( 'jquery.modal-js', plugins_url( 'advanced-custom-post-type/assets/vendor/jquery.modal/jquery.modal.min.js'), [], '3.1.0', true);
        wp_enqueue_style( 'jquery.modal-css', plugins_url( 'advanced-custom-post-type/assets/vendor/jquery.modal/jquery.modal.min.css'), [], '3.1.0', 'all');
        wp_enqueue_script( 'custom-post-js', plugins_url( ACPT_DEV_MODE ? 'advanced-custom-post-type/assets/static/js/post.js' : 'advanced-custom-post-type/assets/static/js/post.min.js'), [], '1.0.0', true);
    }
}
