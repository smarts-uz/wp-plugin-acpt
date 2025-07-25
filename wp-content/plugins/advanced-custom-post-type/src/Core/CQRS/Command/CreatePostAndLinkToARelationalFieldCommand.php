<?php

namespace ACPT\Core\CQRS\Command;

use ACPT\Core\Models\Meta\MetaFieldModel;

class CreatePostAndLinkToARelationalFieldCommand implements CommandInterface
{
    private $value;
    private MetaFieldModel $metaField;
    private $entityType;
    private $entityValue;
    private $entityId;
    private $savedValues;

    /**
     * CreatePostAndLinkToARelationalFieldCommand constructor.
     * @param MetaFieldModel $metaField
     * @param $newPostId
     * @param $entityType
     * @param $entityValue
     * @param $entityId
     * @param $savedValues
     */
    public function __construct(MetaFieldModel $metaField, $newPostId, $entityType, $entityValue, $entityId, $savedValues = null)
    {
        $this->metaField = $metaField;
        $this->value = $newPostId;
        $this->entityType = $entityType;
        $this->entityValue = $entityValue;
        $this->entityId = $entityId;
        $this->savedValues = $savedValues;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        try {
            $postId = wp_insert_post([
                'post_title' =>  $this->value,
                'post_content' =>  '',
                'post_type' =>  $this->entityValue,
                'post_status' => 'publish',
            ]);

            if (is_wp_error($postId)) {
                return false;
            }

            $newValues = [$postId];

            if($this->metaField->getRelations()[0]->isMany()){
                $newValues = array_merge($newValues, explode(",", $this->savedValues));
            }

            $command = new HandleRelationsCommand($this->metaField, $newValues, $this->entityId, $this->entityType);
            $command->execute();

            return true;
        } catch (\Exception $exception){
            return false;
        }
    }
}