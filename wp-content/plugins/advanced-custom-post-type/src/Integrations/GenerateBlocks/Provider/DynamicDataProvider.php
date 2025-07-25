<?php

namespace ACPT\Integrations\GenerateBlocks\Provider;

use ACPT\Core\Models\Belong\BelongModel;
use ACPT\Core\Models\Meta\MetaGroupModel;
use ACPT\Core\Repository\MetaRepository;
use ACPT\Integrations\GenerateBlocks\Provider\Fields\AbstractField;
use GenerateBlocks_Dynamic_Tag_Callbacks;
use GenerateBlocks_Dynamic_Tags;
use GenerateBlocks_Singleton;
use GenerateBlocks_Register_Dynamic_Tag;

class DynamicDataProvider extends GenerateBlocks_Singleton
{
    /**
     * Initialize all hooks.
     *
     * @return void
     */
    public function init()
    {
        if ( ! class_exists( 'GenerateBlocks_Register_Dynamic_Tag' ) ) {
            return;
        }

        add_action( 'init', [ $this, 'register' ] );
    }
    /**
     * Register the tags.
     *
     * @return void
     */
    public function register()
    {
        try {
            $fieldGroups = MetaRepository::get([
                'clonedFields' => true
            ]);

            foreach ($fieldGroups as $fieldGroup){
                if(count($fieldGroup->getBelongs()) > 0){
                    foreach ($fieldGroup->getBelongs() as $belong){
                        $this->registerFields($belong, $fieldGroup);
                    }
                }
            }
        } catch (\Exception $exception){}
    }

    /**
     * @param BelongModel $belong
     * @param MetaGroupModel $fieldGroup
     */
    private function registerFields(BelongModel $belong, MetaGroupModel $fieldGroup)
    {
        foreach ($fieldGroup->getBoxes() as $box){
            foreach ($box->getFields() as $field){

                $field->setBelongsToLabel($belong->getBelongsTo());
                $field->setFindLabel($belong->getFind());

                $className = 'ACPT\\Integrations\\GenerateBlocks\\Provider\\Fields\\'.$field->getType().'Field';

                if(class_exists($className)){
                    /** @var AbstractField $instance */
                    $instance = new $className($belong, $field);
                    $instance->register();
                }
            }
        }
    }
}
