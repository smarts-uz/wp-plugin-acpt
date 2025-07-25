<?php

namespace ACPT\Utils\Checker;

use ACPT\Constants\BelongsTo;
use ACPT\Constants\MetaTypes;
use ACPT\Constants\Operator;
use ACPT\Core\Models\Meta\MetaGroupModel;
use ACPT\Utils\Data\Meta;
use ACPT\Utils\Wordpress\Terms;

class MetaGroupVisibilityChecker
{
    /**
     * This method is a helper to check the visibility of a meta group by generic arguments
     * Used by get_acpt_fields() method
     *
     * @param MetaGroupModel $group
     * @param array $args
     * @return bool
     */
    public static function fromArguments(MetaGroupModel $group, $args = [])
    {
        $allowedDiscriminators = [
            'post_id',
            'term_id',
            'user_id',
            'comment_id',
            'option_page',
        ];

        if(isset($args['post_id'])){
            $discriminator = 'post_id';
            $value = $args['post_id'];
        } elseif(isset($args['term_id'])){
            $discriminator = 'term_id';
            $value = $args['term_id'];
        } elseif(isset($args['user_id'])){
            $discriminator = 'user_id';
            $value = $args['user_id'];
        } elseif(isset($args['comment_id'])){
            $discriminator = 'comment_id';
            $value = $args['comment_id'];
        } elseif(isset($args['option_page'])){
            $discriminator = 'option_page';
            $value = $args['option_page'];
        }

        if(!isset($discriminator)){
            return false;
        }

        if(!isset($value)){
            return false;
        }

        if(!in_array($discriminator, $allowedDiscriminators)){
            return false;
        }

        $conditions = [];

        switch ($discriminator){

            case 'comment_id':
            case 'post_id':
                $conditions[] = [
                    'belongsTo' => MetaTypes::CUSTOM_POST_TYPE,
                    'find' => get_post_type($value),
                ];
                $conditions[] = [
                    'belongsTo' => BelongsTo::POST_ID,
                    'find' => (int)$value,
                ];
                $conditions[] = [
                    'belongsTo' => BelongsTo::PARENT_POST_ID,
                    'find' => (int)$value,
                ];
                break;

            case 'term_id':
                $term = get_term($value);

                if($term instanceof \WP_Term){
                    $conditions[] = [
                        'belongsTo' => MetaTypes::TAXONOMY,
                        'find' => $term->taxonomy,
                    ];
                    $conditions[] = [
                        'belongsTo' => BelongsTo::TERM_ID,
                        'find' => (int)$value,
                    ];
                }
                break;

            case 'user_id':
                $conditions[] = [
                    'belongsTo' => MetaTypes::USER,
                    'find' => null,
                ];
                $conditions[] = [
                    'belongsTo' => BelongsTo::USER_ID,
                    'find' => (int)$value,
                ];
                break;

            case 'option_page':
                $conditions[] = [
                    'belongsTo' => MetaTypes::OPTION_PAGE,
                    'find' => (string)$value,
                ];
                break;
        }

        $matches = 0;

        foreach ($conditions as $condition){
            if(self::isVisible($group, $condition['belongsTo'], $condition['find'])){
                $matches++;
            }
        }

        return $matches > 0;
    }

	/**
	 * @param MetaGroupModel $group
	 * @param string $belongsTo
	 * @param null $find
	 *
	 * @return bool
	 */
	public static function isVisible(MetaGroupModel $group, $belongsTo, $find = null): bool
	{
		try {
			if($find !== null) {
                if ($group->belongsTo($belongsTo, Operator::EQUALS, (string)$find)) {
                    return true;
                }

                switch ($belongsTo){

                    case BelongsTo::POST_ID:

                        foreach ($group->getBelongs() as $belongModel) {
                            $operator = $belongModel->getOperator();

                            if ($group->belongsTo(BelongsTo::POST_ID, $operator, (string)$find)) {
                                return true;
                            }
                        }

                        break;

                    // CUSTOM_POST_TYPE
                    case MetaTypes::CUSTOM_POST_TYPE:

                        // this is the current post we are editing
                        $postId = $_GET['post'] ?? $_POST['post_ID'] ?? null;
                        $post = get_post($postId);

                        if ($post instanceof \WP_Post) {
                            foreach ($group->getBelongs() as $belongModel) {

                                $operator = $belongModel->getOperator();

                                switch ($belongModel->getBelongsTo()) {

                                    // 2. POST_ID
                                    case BelongsTo::POST_ID:

                                        if ($group->belongsTo(BelongsTo::POST_ID, $operator, (string)$post->ID)) {
                                            return true;
                                        }

                                        break;

                                    // 3. PARENT_POST_ID
                                    case BelongsTo::PARENT_POST_ID:

                                        $postParent = get_post_parent($post->ID);

                                        if($postParent !== null){
                                            if ($group->belongsTo(BelongsTo::PARENT_POST_ID, $operator, (string)$postParent->ID)) {
                                                return true;
                                            }
                                        } else {
                                            if ($group->belongsTo(BelongsTo::PARENT_POST_ID, $operator, (string)$post->ID)) {
                                                return true;
                                            }
                                        }

                                        break;

                                    // 4. POST_TEMPLATE
                                    case BelongsTo::POST_TEMPLATE:

                                        $file = Meta::fetch($post->ID, MetaTypes::CUSTOM_POST_TYPE, '_wp_page_template', true);
                                        if ($group->belongsTo(BelongsTo::POST_TEMPLATE, $operator, $file)) {
                                            return true;
                                        }

                                        break;

                                    // 5. POST_TAX
                                    // 6. POST_CAT
                                    case BelongsTo::POST_TAX:
                                    case BelongsTo::POST_CAT:
                                        $terms = Terms::getForPostId($post->ID);
                                        $conditions = [];

                                        if(!empty($terms)){
                                            foreach ($terms as $term) {
                                                $conditions[] = $group->belongsTo($belongModel->getBelongsTo(), $operator, $term->term_taxonomy_id);
                                            }
                                        } else {
                                            if($operator === Operator::NOT_IN or $operator === Operator::NOT_EQUALS){
                                                $conditions[] = $group->belongsTo($belongModel->getBelongsTo(), $operator, "999999999999999999999999999999999");
                                            }
                                        }

                                        if(!empty($conditions)){
                                            if($operator === Operator::EQUALS or $operator === Operator::NOT_EQUALS){
                                                if(!in_array(false, $conditions)){
                                                    return true;
                                                }
                                            } else {
                                                if(in_array(true, $conditions)){
                                                    return true;
                                                }
                                            }
                                        }

                                        break;
                                }
                            }
                        }

                        break;

                    // TAXONOMY
                    case MetaTypes::TAXONOMY:
                        if(isset($_GET['tag_ID'])){
                            foreach ($group->getBelongs() as $belongModel) {

                                $operator = $belongModel->getOperator();

                                if($belongModel->getBelongsTo() === BelongsTo::TERM_ID){
                                    if ($group->belongsTo(BelongsTo::TERM_ID, $operator, (string)$_GET['tag_ID'])) {
                                        return true;
                                    }
                                }
                            }
                        }

                        break;

                    // USER
                    case MetaTypes::USER:

                        $userId = (isset($_GET['user_id'])) ? $_GET['user_id'] : get_current_user_id();

                        if(!empty($userId)){
                            foreach ($group->getBelongs() as $belongModel) {

                                $operator = $belongModel->getOperator();

                                if($belongModel->getBelongsTo() === BelongsTo::USER_ID){
                                    if ($group->belongsTo(BelongsTo::USER_ID, $operator, (string)$userId)) {
                                        return true;
                                    }
                                }
                            }
                        }

                        break;
                }

				return false;
			}

			return $group->belongsTo($belongsTo);
		} catch (\Exception $exception){
			return false;
		}
	}
}