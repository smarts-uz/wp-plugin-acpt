<?php

namespace ACPT\Integrations\ElementorPro\Tags;

use ACPT\Core\Models\Meta\MetaFieldModel;
use ACPT\Utils\PHP\Email;
use ACPT\Utils\PHP\Phone;
use ACPT\Utils\Wordpress\WPAttachment;
use Elementor\Modules\DynamicTags\Module;

class ACPTUrlTag extends ACPTAbstractTag
{
	/**
	 * @inheritDoc
	 */
	public function get_categories()
	{
		return [
			Module::URL_CATEGORY,
		];
	}

	/**
	 * @inheritDoc
	 */
	public function get_name()
	{
		return 'acpt-url';
	}

	/**
	 * @inheritDoc
	 */
	public function get_title()
	{
		return esc_html__( "ACPT URL field", ACPT_PLUGIN_NAME );
	}

	public function render()
	{
		$render = '';
		$field = $this->extractField();

		if(!empty($field)){
            $rawData = $this->getRawData();
            $fieldType = $field['fieldType'];

            switch ($fieldType){

                case MetaFieldModel::AUDIO_TYPE:
                    if($rawData instanceof WPAttachment){
                        $render .= $rawData->getSrc();
                    }
                    break;

                case MetaFieldModel::QR_CODE_TYPE:

                    if(isset($rawData['url'])){
                        $render .= $rawData['url'];
                    }

                    break;

                case MetaFieldModel::EMBED_TYPE:
                    $render .= $rawData;
                    break;

                case MetaFieldModel::EMAIL_TYPE:
                    if(!empty($rawData) and is_string($rawData)){
                        $render .= 'mailto:'.Email::sanitize($rawData);
                    }
                    break;

                case MetaFieldModel::PHONE_TYPE:
                    $render .= Phone::format($rawData, null, Phone::FORMAT_RFC3966);
                    break;

                case MetaFieldModel::URL_TYPE:
                    if(!empty($rawData) and is_array($rawData) and isset($rawData['url'])){
                        $render .= $rawData['url'];
                    }
                    break;
            }
		}

		echo $render;
	}
}