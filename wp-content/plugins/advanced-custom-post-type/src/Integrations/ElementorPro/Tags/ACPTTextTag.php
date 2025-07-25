<?php

namespace ACPT\Integrations\ElementorPro\Tags;

use ACPT\Core\Generators\Meta\TableFieldGenerator;
use ACPT\Core\Helper\Strings;
use ACPT\Core\Models\Meta\MetaFieldModel;
use ACPT\Utils\PHP\Barcode;
use ACPT\Utils\PHP\QRCode;
use ACPT\Utils\Wordpress\WPAttachment;
use Elementor\Modules\DynamicTags\Module;

class ACPTTextTag extends ACPTAbstractTag
{
	/**
	 * @inheritDoc
	 */
	public function get_categories()
	{
		return [
			Module::TEXT_CATEGORY,
		];
	}

	/**
	 * @inheritDoc
	 */
	public function get_name()
	{
		return 'acpt-text';
	}

	/**
	 * @inheritDoc
	 */
	public function get_title()
	{
		return esc_html__( "ACPT text field", ACPT_PLUGIN_NAME );
	}

	public function render()
	{
		$render = '';
		$field = $this->extractField();

		if(!empty($field)){
		    $rawData = $this->getRawData();
            $fieldType = $field['fieldType'];

            switch ($fieldType){

                // AUDIO
                case MetaFieldModel::AUDIO_TYPE:
                    if($rawData instanceof WPAttachment){
                        $render .= $rawData->getTitle();
                    }
                    break;

                // BARCODE_TYPE
                case MetaFieldModel::BARCODE_TYPE:
                    if(!empty($rawData)){
                        $render .= Barcode::render($rawData);
                    }
                    break;

                // QR_CODE_TYPE
                case MetaFieldModel::QR_CODE_TYPE:
                    if(!empty($rawData)){
                        $render .= QRCode::render($rawData);
                    }
                    break;

                // RATING_TYPE
                case MetaFieldModel::RATING_TYPE:
                    if(!empty($rawData)){
                        $render .= ($rawData/2) . "/5";
                    }
                    break;

                // CHECKBOX_TYPE
                // SELECT_MULTI_TYPE
                case MetaFieldModel::CHECKBOX_TYPE:
                case MetaFieldModel::SELECT_MULTI_TYPE:
                    if(!empty($rawData) and is_array($rawData)){
                        $render .= implode(",", $rawData);
                    }
                    break;

                // COUNTRY_TYPE
                case MetaFieldModel::COUNTRY_TYPE:
                    if(!empty($rawData) and is_array($rawData) and isset($rawData['value'])){
                        $render .= $rawData['value'];
                    }
                    break;

                // TABLE_TYPE
                case MetaFieldModel::TABLE_TYPE:
                    if(is_string($rawData) and Strings::isJson($rawData)){
                        $generator = new TableFieldGenerator($rawData);
                        $render .= $generator->generate();
                    }
                    break;

                // URL_TYPE
                case MetaFieldModel::URL_TYPE:
                    if(!empty($rawData) and is_array($rawData) and isset($rawData['url'])){
                        $render .= $rawData['label'] ?? $rawData['url'];
                    }
                    break;

                default:

                    // Fix for Numeric fields
                    if(is_numeric($rawData)){
                        $rawData = (string)$rawData;
                    }

                    if(is_string($rawData)){
                        $render .= $rawData;
                    }
                    break;
            }
        }

		echo $render;
	}
}
