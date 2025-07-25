<?php

namespace ACPT\Integrations\ElementorPro\Tags;

use ACPT\Core\Models\Meta\MetaFieldModel;
use Elementor\Modules\DynamicTags\Module;

class ACPTNumberTag extends ACPTAbstractTag
{
	/**
	 * @inheritDoc
	 */
	public function get_categories()
	{
		return [
			Module::NUMBER_CATEGORY,
		];
	}

	/**
	 * @inheritDoc
	 */
	public function get_name()
	{
		return 'acpt-number';
	}

	/**
	 * @inheritDoc
	 */
	public function get_title()
	{
		return esc_html__( "ACPT number field", ACPT_PLUGIN_NAME );
	}

	public function render()
	{
		$render = '';
		$field = $this->extractField();

		if(!empty($field)){
            $rawData = $this->getRawData();
            $fieldType = $field['fieldType'];

            switch ($fieldType){
                case MetaFieldModel::RATING_TYPE:
                    $render .= $rawData/2;
                    break;

                default:
                    $render .= (int)$rawData;
                    break;
            }
		}

		echo $render;
	}
}