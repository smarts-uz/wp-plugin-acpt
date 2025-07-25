<?php

namespace ACPT\Integrations\WPGridBuilder\Provider;

use ACPT\Core\Generators\Meta\TableFieldGenerator;
use ACPT\Core\Helper\Strings;
use ACPT\Core\Models\Meta\MetaFieldModel;
use ACPT\Core\Repository\MetaRepository;
use ACPT\Utils\PHP\Email;
use ACPT\Utils\PHP\Numbers;
use ACPT\Utils\PHP\Phone;
use ACPT\Utils\PHP\Url;
use ACPT\Utils\Wordpress\Users;
use ACPT\Utils\Wordpress\WPAttachment;
use ACPT\Utils\Wordpress\WPUtils;

class WPGridBuilderDataProvider
{
	/**
	 * @var array
	 */
	private array $fields = [];

	public function __construct()
	{
		add_filter( 'wp_grid_builder/custom_fields', [ $this, 'customFields' ], 10, 2 );
		add_filter( 'wp_grid_builder/facet/sort_query_vars', [ $this, 'sortQueryVars' ] );
		add_filter( 'wp_grid_builder/block/custom_field', [ $this, 'customFieldBlock' ], 10, 2 );
		add_filter( 'wp_grid_builder/indexer/index_object', [ $this, 'indexObject' ], 10, 3 );
		add_filter( 'wp_grid_builder/metadata', [ $this, 'metadataValue' ], 10, 4 );
	}

	/**
	 * $key is the unique field identifier
	 *
	 * @param string $key
	 *
	 * @return void
	 */
	private function setFields($key = 'key')
	{
		try {
			$fields = [];
			$groups = MetaRepository::get([
			    'clonedFields' => true
            ]);

			foreach ($groups as $group){
				foreach ($group->getBoxes() as $box){
					foreach ($box->getFields() as $field){

						$nowAllowed = [
							MetaFieldModel::FLEXIBLE_CONTENT_TYPE,
							MetaFieldModel::REPEATER_TYPE,
							MetaFieldModel::CLONE_TYPE,
						];

						if(!in_array($field->getType(), $nowAllowed)){
							$fields[] = [
								$key => $this->getFieldKey($field),
								'id' => $field->getId(),
								'label' => 'ACPT > ' . $field->getUiName(),
								'type' => $field->getType(),
								'box' => $field->getBox()->getName(),
								'field' => $field->getName(),
							];
						}

						// register nested fields
						if($field->hasChildren()){
							foreach ($field->getChildren() as $child){
								$fields[] = [
									$key => $this->getFieldKey($child),
									'id' => $child->getId(),
									'label' => 'ACPT > ' . $child->getUiName(),
									'type' => $child->getType(),
									'box' => $child->getBox()->getName(),
									'field' => $child->getName(),
									'parent' => $field->getName(),
								];
							}
						}
					}
				}
			}

			$this->fields = $fields;
		} catch (\Exception $exception){
			$this->fields = [];
		}
	}

	/**
	 * @param MetaFieldModel $fieldModel
	 *
	 * @return string
	 */
	private function getFieldKey(MetaFieldModel $fieldModel)
	{
		if($fieldModel->getParentField() !== null){
			return "acpt_". $fieldModel->getParentField()->getDbName(). "_" . Strings::toDBFormat($fieldModel->getName());
		}

		return "acpt_". $fieldModel->getDbName();
	}

	/**
	 * @param $fieldId
	 *
	 * @return mixed|null
	 */
	private function getField($fieldId)
	{
		$filtered = array_filter($this->fields, function ($f) use($fieldId){
			return $f['name'] === $fieldId;
		});

		if(empty($filtered)){
			return null;
		}

		return array_values($filtered)[0];
	}

	/**
	 * Retrieve all ACPT fields
	 *
	 * @param array $fields Holds registered custom fields.
	 * @param string $key Key type to retrieve.
	 *
	 * @return array
	 * @throws \Exception
	 */
	public function customFields($fields, $key = 'key')
	{
		$this->setFields($key);

		if (!empty($this->fields)) {
			$fields['ACPT'] = array_combine(
				array_column( $this->fields, $key ),
				array_column( $this->fields, 'label' )
			);
		}

		return $fields;
	}

	/**
	 * Change sort query variables
	 *
	 * @param array $queryVars Holds query sort variables.
	 * @return array
	 */
	public function sortQueryVars($queryVars)
	{
		if (empty($queryVars['meta_key'])){
			return $queryVars;
		}

		$queryVars['meta_key'] = str_replace("acpt_","", $queryVars['meta_key']);

		return $queryVars;
	}

	/**
	 * @see https://docs.wpgridbuilder.com/resources/filter-indexer-index-object/
	 *
	 * @param array $rows Holds rows to index.
	 * @param array $objectId Object id to index.
	 * @param array $facet Holds facet settings.
	 *
	 * @return array
	 */
	public function indexObject($rows, $objectId, $facet )
	{
		$source = explode( '/', $facet['source'] );
		$source = reset( $source );

		if ('post_meta' === $source or 'user_meta' === $source or 'term_meta' === $source) {
			$rows = $this->indexACPT($rows, $objectId, $source, $facet);
		}

		return $rows;
	}

	/**
	 * @param $rows
	 * @param $objectId
	 * @param $originalSource
	 * @param $facet
	 *
	 * @return mixed
	 */
	private function indexACPT($rows, $objectId, $originalSource, $facet)
	{
		$source = explode( '/', $facet['source'] );

		if (empty( $source[1])) {
			return $rows;
		}

		if(empty($this->fields)){
			$this->setFields('name');
		}

		$field = $this->getField($source[1]);

		if(empty($field)){
			return $rows;
		}

		$args = [
			'box_name' => $field['box'],
			'field_name' => ((isset($field['parent']) and !empty($field['parent'])) ? $field['parent'] : $field['field']),
		];

		switch ($originalSource){
			case "post_meta":
				$args['post_id'] = $objectId;
				break;
			case "term_meta":
				$args['term_id'] = $objectId;
				break;
			case "user_meta":
				$args['user_id'] = $objectId;
				break;
		}

		if(!empty($field['forged_by'])){
            $args['forged_by'] = $field['forged_by'];
        }

		$rawValue = get_acpt_field($args);

		if(empty($rawValue)){
			return $rows;
		}

		// Handle repeater values.
		if(isset($field['parent'])){

			if(!is_array($rawValue)){
				return null;
			}

			unset($field['parent']);

			foreach ($rawValue as $item){
				if(isset($item[$field['field']])){
					$rows = array_merge($rows, $this->indexField($field, $item[$field['field']]));
				}
			}

		} else {
			$rows = array_merge($rows, $this->indexField($field, $rawValue));
		}


		return $rows;
	}

	/**
	 * @param array $field
	 * @param $rawValue
	 *
	 * @return array
	 */
	private function indexField($field, $rawValue)
	{
	    if(!isset($field['type'])){
	        return [];
        }

		switch ($field['type']){

			// ADDRESS_TYPE
			case MetaFieldModel::ADDRESS_TYPE:
				if (!isset( $rawValue['lat'], $rawValue['lng'])){
					return [];
				}

				return [
					[
						'facet_value' => $rawValue['lat'],
						'facet_name'  => $rawValue['lng'],
					],
				];

			// ADDRESS_MULTI_TYPE
			case MetaFieldModel::ADDRESS_MULTI_TYPE:

				$rows = [];

				if(is_array($rawValue) and !empty($rawValue)){
					foreach ($rawValue as $order => $item){

						if(isset($item['lat']) and isset($item['lng'])){
							$rows[] = [
								'facet_value' => $item['lat'],
								'facet_name'  => $item['lng'],
							];
						}
					}
				}

				return $rows;

			// CHECKBOX_TYPE
			// LIST_TYPE
			// SELECT_MULTI_TYPE
			case MetaFieldModel::CHECKBOX_TYPE:
			case MetaFieldModel::LIST_TYPE:
			case MetaFieldModel::SELECT_MULTI_TYPE:

				$rows = [];

				if(is_array($rawValue) and !empty($rawValue)){
					foreach ($rawValue as $order => $item){

						if (!is_string( $item) ) {
							continue;
						}

						$rows[] = [
							'facet_value' => $item,
							'facet_name'  => $item,
							'facet_order' => $order,
						];
					}
				}

				return $rows;

			// COUNTRY_TYPE
			case MetaFieldModel::COUNTRY_TYPE:

				if(is_array($rawValue) and isset($rawValue['value'])){
					return [
						'facet_value' => $rawValue['value'],
						'facet_name'  => $rawValue['value'],
					];
				}

				return [];

			// CURRENCY_TYPE
			case MetaFieldModel::CURRENCY_TYPE:

				if(
					is_array($rawValue) and
					isset($rawValue['amount']) and
					isset($rawValue['unit'])
				){
                    return [
                        [
                            'facet_value' => $rawValue['amount'],
                            'facet_name'  => $rawValue['amount'] . " " . $rawValue['unit'],
                        ],
                    ];
				}

				return [];

			// DATE_RANGE_TYPE
			case MetaFieldModel::DATE_RANGE_TYPE:

				if(is_array($rawValue) and !empty($rawValue) and count($rawValue) === 2){
					$from = $rawValue[0];
					$to = $rawValue[1];

					$value = $from;
					$value .= ' - ';
					$value .= $to;

                    return [
                        [
                            'facet_value' => $value,
                            'facet_name'  => $value,
                        ],
                    ];
				}

				return [];

			// LENGTH_TYPE
			case MetaFieldModel::LENGTH_TYPE:

				if(
					is_array($rawValue) and
					isset($rawValue['length']) and
					isset($rawValue['unit'])
				){
                    return [
                        [
                            'facet_value' => $rawValue['length'],
                            'facet_name'  => $rawValue['length'] . " " . $rawValue['unit'],
                        ],
                    ];
				}

				return [];

            // POST_OBJECT_TYPE
            case MetaFieldModel::POST_TYPE:

                $rows = [];

                if(is_array($rawValue) and !empty($rawValue)){
                    foreach ($rawValue as $order => $item){
                        if($item instanceof \WP_Post){
                            $rows[] = [
                                'facet_value' => $item->ID,
                                'facet_name'  => $item->post_title,
                                'facet_order' => $order,
                            ];
                        } elseif($item instanceof \WP_Term){
                            $rows[] = [
                                'facet_value' => $item->term_id,
                                'facet_name'  => $item->name,
                                'facet_order' => $order,
                            ];
                        } elseif($item instanceof \WP_User){
                            $rows[] = [
                                'facet_value' => $item->ID,
                                'facet_name'  => Users::getUserLabel($item),
                                'facet_order' => $order,
                            ];
                        }
                    }
                }

                return $rows;

			// POST_OBJECT_TYPE
			case MetaFieldModel::POST_OBJECT_TYPE:

				if($rawValue instanceof \WP_Post){
					return [
						[
							'facet_value' => $rawValue->ID,
							'facet_name'  => $rawValue->post_title,
						],
					];
				}

				return [];

			// POST_OBJECT_MULTI_TYPE
			case MetaFieldModel::POST_OBJECT_MULTI_TYPE:

				$rows = [];

				if(is_array($rawValue) and !empty($rawValue)){
					foreach ($rawValue as $order => $item){

						if($item instanceof \WP_Post){
							$rows[] = [
								'facet_value' => $item->ID,
								'facet_name'  => $item->post_title,
								'facet_order' => $order,
							];
						}
					}
				}

				return $rows;

			// TERM_OBJECT_TYPE
			case MetaFieldModel::TERM_OBJECT_TYPE:

				if($rawValue instanceof \WP_Term){
					return [
						[
							'facet_value' => $rawValue->term_id,
							'facet_name'  => $rawValue->name,
						],
					];
				}

				return [];

			// TERM_OBJECT_MULTI_TYPE
			case MetaFieldModel::TERM_OBJECT_MULTI_TYPE:

				$rows = [];

				if(is_array($rawValue) and !empty($rawValue)){
					foreach ($rawValue as $order => $item){

						if($item instanceof \WP_Term){
							$rows[] = [
								'facet_value' => $item->term_id,
								'facet_name'  => $item->name,
								'facet_order' => $order,
							];
						}
					}
				}

				return $rows;

			// TOGGLE_TYPE
			case MetaFieldModel::TOGGLE_TYPE:

				if($rawValue != 0 or $rawValue != 1){
					return [];
				}

				$name = (int) $rawValue > 0 ? __( 'Yes', 'wp-grid-builder' ) : __( 'No', 'wp-grid-builder' );

				return [
					[
						'facet_value' => $rawValue,
						'facet_name'  => $name,
					],
				];

			// URL_TYPE
			case MetaFieldModel::URL_TYPE:


				if(!is_array($rawValue)){
					return [];
				}

				if(!isset($rawValue['url'])){
					return [];
				}

				$label = (isset($rawValue['url']) and !empty($rawValue['label'])) ? $rawValue['label'] : $rawValue['url'];

				return [
					[
						'facet_value' => Url::sanitize($rawValue['url']),
						'facet_name'  => $label,
					],
				];

			// USER_TYPE
			case MetaFieldModel::USER_TYPE:

				if($rawValue instanceof \WP_User){
					return [
						[
							'facet_value' => $rawValue->ID,
							'facet_name'  => Users::getUserLabel($rawValue),
						],
					];
				}

				return [];

			// USER_MULTI_TYPE
			case MetaFieldModel::USER_MULTI_TYPE:

				$rows = [];

				if(is_array($rawValue) and !empty($rawValue)){
					foreach ($rawValue as $order => $item){

						if($item instanceof \WP_User){
							$rows[] = [
								'facet_value' => $item->ID,
								'facet_name'  => Users::getUserLabel($item),
								'facet_order' => $order,
							];
						}
					}
				}

				return $rows;

			// WEIGHT_TYPE
			case MetaFieldModel::WEIGHT_TYPE:

				if(
					is_array($rawValue) and
					isset($rawValue['weight']) and
					isset($rawValue['unit'])
				){
                    return [
                        [
                            'facet_value' => $rawValue['weight'],
                            'facet_name'  => $rawValue['weight'] . " " . $rawValue['unit'],
                        ],
                    ];
				}

				return [];

			// default, ignore not scalar values
			default:
				if(is_scalar($rawValue)){
					return [
						[
							'facet_value' => $rawValue,
							'facet_name'  => $rawValue,
						],
					];
				}
		}

		return [];
	}

	/**
	 * Return ACPT field value as string
	 *
	 * @param string $output   Custom field output.
	 * @param string $fieldId  Field identifier.
	 * @return mixed
	 */
	public function customFieldBlock($output, $fieldId)
	{
		if(empty($this->fields)){
			$this->setFields('name');
		}

		$field = $this->getField($fieldId);

		if(empty($field)){
			return null;
		}

		$args = [
			'box_name' => $field['box'],
			'field_name' => ((isset($field['parent']) and !empty($field['parent'])) ? $field['parent'] : $field['field']),
		];

		$object = wpgb_get_object();
		$objectType = wpgb_get_object_type();

		switch ($objectType){
			case "post":
				$args['post_id'] = $object->ID;
				break;
			case "term":
				$args['term_id'] = $object->ID;
				break;
			case "user":
				$args['user_id'] = $object->ID;
				break;
		}

        if(!empty($field['forged_by'])){
            $args['forged_by'] = $field['forged_by'];
        }

		$rawValue = get_acpt_field($args);

		return $this->returnValue($field, $rawValue);
	}

	/**
	 * @param $field
	 * @param $rawValue
	 *
	 * @return mixed|null
	 */
	private function returnValue($field, $rawValue)
	{
		if(empty($rawValue)){
			return null;
		}

		$beforeAndAfterContext = get_acpt_meta_field_before_and_after($field['box'], $field['field']);

		// Nested fields in a repeater
		if(isset($field['parent'])){

			if(!is_array($rawValue)){
				return null;
			}

			unset($field['parent']);

			$data = [];

			foreach ($rawValue as $item){
				if(isset($item[$field['field']])){
					$data[] = $this->returnValue($field, $item[$field['field']]);
				}
			}

			return $this->renderList($data);
		}

		switch ($field['type']){

			// ADDRESS_TYPE
			case MetaFieldModel::ADDRESS_TYPE:

				if(is_array($rawValue) and isset($rawValue['address'])){
					return $rawValue['address'];
				}

				return null;

			// ADDRESS_MULTI_TYPE
			case MetaFieldModel::ADDRESS_MULTI_TYPE:

				$address = [];

				if(is_array($rawValue) and !empty($rawValue)){
					foreach ($rawValue as $item){
						if(isset($item['address'])){
							$address[] = '<li>' . $item['address'] . '</li>';
						}
					}
				}

				return $this->renderList($address);

			// CHECKBOX_TYPE
			// LIST_TYPE
			// SELECT_MULTI_TYPE
			case MetaFieldModel::CHECKBOX_TYPE:
			case MetaFieldModel::LIST_TYPE:
			case MetaFieldModel::SELECT_MULTI_TYPE:

				return $this->renderList($rawValue);

			// COUNTRY_TYPE
			case MetaFieldModel::COUNTRY_TYPE:

				if(is_array($rawValue) and isset($rawValue['value'])){
					return $rawValue['value'];
				}

				return null;

			// CURRENCY_TYPE
			case MetaFieldModel::CURRENCY_TYPE:

				if(
					is_array($rawValue) and
					isset($rawValue['amount']) and
					isset($rawValue['unit'])
				){
					$rawValue['amount'] = str_replace([$beforeAndAfterContext['before'], $beforeAndAfterContext['after']], '', $rawValue['amount']);

                    return $beforeAndAfterContext['before'] . $rawValue['amount'] . " " . $rawValue['unit'] . $beforeAndAfterContext['after'];
				}

				return null;

			// DATE_RANGE_TYPE
			case MetaFieldModel::DATE_RANGE_TYPE:

				if(is_array($rawValue) and !empty($rawValue) and count($rawValue) === 2){
					$from = $rawValue[0];
					$to = $rawValue[1];

					$value = str_replace([$beforeAndAfterContext['before'], $beforeAndAfterContext['after']], '', $from);
					$value .= ' - ';
					$value .= str_replace([$beforeAndAfterContext['before'], $beforeAndAfterContext['after']], '', $to);

					return $value;
				}

				return null;

			// EMAIL_TYPE
			case MetaFieldModel::EMAIL_TYPE:

				if(is_string($rawValue)){
					$email_url = str_replace([$beforeAndAfterContext['before'], $beforeAndAfterContext['after']], '', $rawValue);

					return Email::sanitize($email_url);
				}

				return null;

			// FILE_TYPE
			case MetaFieldModel::FILE_TYPE:

				if(
					is_array($rawValue) and
					isset($rawValue['file']) and
					$rawValue['file'] instanceof WPAttachment
				){
					$after = $rawValue['after'];
					$before = $rawValue['before'];
					$label = (isset($rawValue['label']) and !empty($rawValue['label'])) ? $rawValue['label'] : $rawValue['file'];
					$src = $rawValue['file']->getSrc();

					$link = '<a href="'.$src.'" target="_blank">';

					if(!empty($before)){
						$link .= $before. ' ';
					}

					$link .= $label;

					if(!empty($after)){
						$link .= ' ' . $after;
					}

					$link .= '</a>';

					return $link;
				}

				return null;

			// GALLERY_TYPE
			case MetaFieldModel::GALLERY_TYPE:

				$images = [];

				if(is_array($rawValue) and !empty($rawValue)){
					foreach ($rawValue as $img){
						if($img instanceof WPAttachment){
							$images[] = '<img src="'.$img->getSrc().'" alt="'.$img->getAlt().'" />';
						}
					}
				}

				return $this->renderList($images);

			// IMAGE_TYPE
			case MetaFieldModel::IMAGE_TYPE:
				if($rawValue instanceof WPAttachment and $rawValue->isImage()){
					return '<img src="'.$rawValue->getSrc().'" alt="'.$rawValue->getAlt().'" />';
				}

				return null;

			// NUMBER_TYPE
			case MetaFieldModel::NUMBER_TYPE:

				if(is_numeric($rawValue)){
					return $rawValue;
				}

				return null;

			// LENGTH_TYPE
			case MetaFieldModel::LENGTH_TYPE:

				if(
					is_array($rawValue) and
					isset($rawValue['length']) and
					isset($rawValue['unit'])
				){
					$rawValue['length'] = str_replace([$beforeAndAfterContext['before'], $beforeAndAfterContext['after']], '', $rawValue['length']);

					return $beforeAndAfterContext['before'] . $rawValue['length'] . " " . $rawValue['unit'] . $beforeAndAfterContext['after'];
				}

				return null;

			// PHONE_TYPE
			case MetaFieldModel::PHONE_TYPE:

				if(is_string($rawValue)){
					$phone_url = str_replace([$beforeAndAfterContext['before'], $beforeAndAfterContext['after']], '', $rawValue);

					return Phone::url($phone_url);
				}

				return null;

            // POST_OBJECT_TYPE
            case MetaFieldModel::POST_TYPE:

                if(is_array($rawValue) and !empty($rawValue)){

                    $data = [];

                    foreach ($rawValue as $item){
                        if($item instanceof \WP_Post){
                            $data[] = $item->post_title;
                        } elseif($item instanceof \WP_Term){
                            $data[] = $item->name;
                        } elseif($item instanceof \WP_User){
                            $data[] = Users::getUserLabel($item);
                        }
                    }

                    return $this->renderList($data);
                }

                return null;

			// POST_OBJECT_TYPE
			case MetaFieldModel::POST_OBJECT_TYPE:

				if($rawValue instanceof \WP_Post){
					return $rawValue->post_title;
				}

				return null;

			// POST_OBJECT_TYPE
			case MetaFieldModel::POST_OBJECT_MULTI_TYPE:

				$posts = [];

				if(is_array($rawValue) and !empty($rawValue)){
					foreach ($rawValue as $post){
						if($post instanceof \WP_Post){
							$posts[] = $post->post_title;
						}
					}

				}

				return $this->renderList($posts);

			// RATING_TYPE
			case MetaFieldModel::RATING_TYPE:

				if(is_numeric($rawValue)){
					return ($rawValue/2) . "/5";
				}

				return null;

			// TABLE_TYPE
			case MetaFieldModel::TABLE_TYPE:

				if(is_string($rawValue) and Strings::isJson($rawValue)){
					$generator = new TableFieldGenerator($rawValue);

					return $generator->generate();
				}

				return null;

			// TERM_OBJECT_TYPE
			case MetaFieldModel::TERM_OBJECT_TYPE:

				if($rawValue instanceof \WP_Term){
					return $rawValue->name;
				}

				return null;

			// TERM_OBJECT_MULTI_TYPE
			case MetaFieldModel::TERM_OBJECT_MULTI_TYPE:

				$terms = [];

				if(is_array($rawValue) and !empty($rawValue)){
					foreach ($rawValue as $term){
						if($term instanceof \WP_Term){
							$terms[] = $term->name;
						}
					}

				}

				return $this->renderList($terms);

			// TEXTAREA_TYPE
			case MetaFieldModel::EDITOR_TYPE:
			case MetaFieldModel::TEXTAREA_TYPE:

				if(is_string($rawValue)){
					return WPUtils::renderShortCode($rawValue, true);
				}

				return null;

			// URL_TYPE
			case MetaFieldModel::URL_TYPE:

				if(!is_array($rawValue)){
					return null;
				}

				if(!isset($rawValue['url'])){
					return null;
				}

				return Url::sanitize($rawValue['url']);

			// USER_TYPE
			case MetaFieldModel::USER_TYPE:

				if($rawValue instanceof \WP_User){
					return Users::getUserLabel($rawValue);
				}

				return null;

			// USER_MULTI_TYPE
			case MetaFieldModel::USER_MULTI_TYPE:

				$users = [];

				if(is_array($rawValue) and !empty($rawValue)){
					foreach ($rawValue as $user){
						if($user instanceof \WP_User){
							$users[] = Users::getUserLabel($user);
						}
					}

				}

				return $this->renderList($users);

			// VIDEO_TYPE
			case MetaFieldModel::VIDEO_TYPE;

				if($rawValue instanceof WPAttachment and $rawValue->isVideo()){

                    return $rawValue->render([
                        'type' => 'video/mp4',
                    ]);
				}

				return null;

			// WEIGHT_TYPE
			case MetaFieldModel::WEIGHT_TYPE:

				if(
					is_array($rawValue) and
					isset($raw_acpt_value['weight']) and
					isset($raw_acpt_value['unit'])
				){
					$rawValue['weight'] = str_replace([$beforeAndAfterContext['before'], $beforeAndAfterContext['after']], '', $rawValue['weight']);

					return $beforeAndAfterContext['before'] . $rawValue['weight'] . " " . $rawValue['unit'] . $beforeAndAfterContext['after'];
				}

				return null;

			default:
				return $rawValue;
		}
	}

	/**
	 * @param $items
	 *
	 * @return string|null
	 */
	private function renderList($items)
	{
		if(!is_array($items)){
			return null;
		}

		if(empty($items)){
			return null;
		}

		$ul = '<ul>';

		foreach ($items as $item){
			$ul .= '<li>' . $item . '</li>';
		}

		$ul .= '</ul>';

		return $ul;
	}

	/**
	 * Return ACPT field value
	 *
	 * @param string  $output    Custom field output.
	 * @param string  $meta_type Type of object metadata is for.
	 * @param integer $object_id ID of the object metadata is for.
	 * @param string  $meta_key  Metadata key.
	 * @return mixed
	 */
	public function metadataValue($output, $meta_type, $object_id, $meta_key)
	{
		$field = explode( 'acpt_', $meta_key );

		if (empty($field[1])) {
			return false;
		}

		// @TODO repeater values ???

		return get_metadata( $meta_type, $object_id, $field[1], true);
	}
}
