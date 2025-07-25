<?php

namespace ACPT\Integrations\GenerateBlocks\Provider;

use ACPT\Constants\MetaTypes;
use ACPT\Core\Models\Meta\MetaFieldModel;
use ACPT\Utils\Data\DataAggregator;
use ACPT\Utils\Data\Meta;
use GenerateBlocks_Pro_Singleton;

class DynamicTags extends GenerateBlocks_Pro_Singleton
{
    /**
     * Init the tags
     */
    public function init()
    {
        add_filter(
            'generateblocks_get_meta_pre_value',
            [ $this, 'getMetaPreValue' ],
            10,
            5
        );

        add_filter(
            'generateblocks_dynamic_tags_post_record_response',
            [ $this, 'addMetaToPostRecord' ],
            10,
            3
        );

        add_filter(
            'generateblocks_dynamic_tags_user_record_response',
            [ $this, 'addMetaToUserRecord' ],
            10,
            3
        );
    }

    /**
     * Add appropriate meta pre values.
     *
     * @param $pre_value
     * @param $id
     * @param $key
     * @param $callable
     *
     * @return array
     */
    public function getMetaPreValue( $pre_value, $id, $key, $callable )
    {
        $belongsTo = null;

        switch ( $callable ) {
            case 'get_post_meta':
                $belongsTo = MetaTypes::CUSTOM_POST_TYPE;
                break;

            case 'get_user_meta':
                $belongsTo = MetaTypes::USER;
                break;

            case 'get_term_meta':
                $belongsTo = MetaTypes::TAXONOMY;
                break;

            case 'get_option':
                $belongsTo = MetaTypes::OPTION_PAGE;
                break;
        }

        Meta::fetch($id, $belongsTo, $key);

        if($belongsTo){
            $value = Meta::fetch($id, $belongsTo, $key);
            $type = Meta::fetch($id, $belongsTo, $key."_type");

            // Repeaters MUST be normalized
            if($type === MetaFieldModel::REPEATER_TYPE){

                $values = [];
                $data = DataAggregator::aggregateNestedFieldsData($value);

                foreach ($data as $index => $datum){
                    foreach ($datum as $d){
                        if(isset($d['key']) and isset($d['value'])){
                            $values[$index][$d['key']] = $d['value'];
                        }
                    }
                }

                return $values;
            }
        }

        return $pre_value;
    }

    /**
     * Filters the post record to include ACPT meta field keys and values.
     *
     * @param object $response Post object from the response.
     * @param int    $id ID of the post record.
     *
     * @return mixed
     */
    public function addMetaToPostRecord( $response, $id ) {

        $meta = get_acpt_fields([
            'post_id' => $id
        ]);

        if ( $meta ) {

            $response->acpt = array_filter(
                    $meta,
                    function ( $key ) {
                        return strpos( $key, '_' ) !== 0;
                    },
                    ARRAY_FILTER_USE_KEY
            );

        }

        return $response;
    }

    /**
     * Filters the user record to include ACPT meta field keys and values.
     *
     * @param object $response Post object from the response.
     * @param int    $id ID of the post record.
     *
     * @return mixed
     */
    public function addMetaToUserRecord( $response, $id ) {

        $meta = get_acpt_fields([
            'user_id' => $id
        ]);

        if ( $meta ) {
            $response->acpt = array_filter(
                $meta,
                function ( $key ) {
                    return strpos( $key, '_' ) !== 0;
                },
                ARRAY_FILTER_USE_KEY
            );
        }

        return $response;
    }
}
