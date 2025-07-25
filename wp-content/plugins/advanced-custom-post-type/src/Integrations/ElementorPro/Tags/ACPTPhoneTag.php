<?php

namespace ACPT\Integrations\ElementorPro\Tags;

use ACPT\Core\Helper\Currencies;
use ACPT\Core\Helper\Lengths;
use ACPT\Core\Helper\Weights;
use ACPT\Core\Models\Meta\MetaFieldModel;
use ACPT\Utils\PHP\Phone;
use ACPT\Utils\Wordpress\Translator;
use Elementor\Controls_Manager;
use Elementor\Modules\DynamicTags\Module;

class ACPTPhoneTag extends ACPTAbstractTag
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
		return 'acpt-unit-of-measure';
	}

	/**
	 * @inheritDoc
	 */
	public function get_title()
	{
		return esc_html__( "ACPT phone field", ACPT_PLUGIN_NAME );
	}

	public function register_controls()
	{
		parent::register_controls();

		$this->add_control(
			'format',
			[
				'label' => Translator::translate( 'Phone format' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					Phone::FORMAT_E164 => Translator::translate(Phone::FORMAT_E164),
					Phone::FORMAT_INTERNATIONAL => Translator::translate(Phone::FORMAT_INTERNATIONAL),
					Phone::FORMAT_NATIONAL => Translator::translate(Phone::FORMAT_NATIONAL),
					Phone::FORMAT_RFC3966 => Translator::translate(Phone::FORMAT_RFC3966),
				],
			]
		);
	}

	public function render()
	{
		$render = '';
		$field = $this->extractField();

		if(!empty($field)){
            $rawData = $this->getRawData();
            $fieldType = $field['fieldType'];

            switch ($fieldType){

                case MetaFieldModel::PHONE_TYPE:

                    $format = (!empty($this->get_settings('format'))) ? $this->get_settings('format') : Phone::FORMAT_E164;

                    if(is_scalar($rawData)){
                        $render .= Phone::format($rawData, null, $format);
                    }

                    break;
            }
		}

		echo $render;
	}

	/**
	 * @param $value
	 * @param $unit
	 * @param $type
	 *
	 * @return mixed
	 */
	private function renderUom($value, $unit, $type)
	{
		$render = (!empty($this->get_settings('render'))) ? $this->get_settings('render') : null;
		$decimalPoints = (!empty($this->get_settings('value_format_decimal_points'))) ? $this->get_settings('value_format_decimal_points') : 0;
		$decimalSeparator = (!empty($this->get_settings('value_format_decimal_separator'))) ? $this->get_settings('value_format_decimal_separator') : ".";
		$thousandsSeparator = (!empty($this->get_settings('value_format_thousands_separator'))) ? $this->get_settings('value_format_thousands_separator') : ",";
		$uomFormat = (!empty($this->get_settings('uom_format'))) ? $this->get_settings('uom_format') : "full";
		$uomPosition = (!empty($this->get_settings('uom_position'))) ? $this->get_settings('uom_position') : "after";

		$value = number_format($value, (int)$decimalPoints, $decimalSeparator, $thousandsSeparator);

		if($uomFormat === 'abbreviation'){
			switch ($type){
				case 'currency':
					$unit = Currencies::getSymbol($unit);
					break;

				case 'length':
					$unit = Lengths::getSymbol($unit);
					break;

				case 'weight':
					$unit = Weights::getSymbol($unit);
					break;
			}
		}

		if($render === 'value'){
			if($value === null){
				return null;
			}

			return $value;
		}

		if($render === 'uom'){
			if($unit === null){
				return null;
			}

			return $unit;
		}

		if($uomPosition === 'before'){
			if($unit === null or $value === null){
				return null;
			}

			return $unit . ' ' . $value;
		}

		if($unit === null or $value === null){
			return null;
		}

		return $value . ' ' . $unit;
	}
}
