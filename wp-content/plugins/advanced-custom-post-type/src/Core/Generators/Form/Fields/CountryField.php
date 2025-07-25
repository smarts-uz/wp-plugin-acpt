<?php

namespace ACPT\Core\Generators\Form\Fields;

use ACPT\Core\Generators\Meta\AfterAndBeforeFieldGenerator;

class CountryField extends AbstractField
{
	/**
	 * @inheritDoc
	 */
	public function render()
	{
        if($this->isNested and $this->fieldModel->getMetaField() !== null){
            $min = $this->fieldModel->getMetaField()->getAdvancedOption("min") ?? null;
            $max = $this->fieldModel->getMetaField()->getAdvancedOption("max") ?? null;
        } else {
            $min = (!empty($this->fieldModel->getExtra()['min'])) ? esc_attr($this->fieldModel->getExtra()['min']) : null;
            $max = (!empty($this->fieldModel->getExtra()['max'])) ? esc_attr($this->fieldModel->getExtra()['max']) : null;
        }

		$field = "
			<input
			    ".$this->disabled()."
				id='".esc_attr($this->getIdName())."'
				name='".esc_attr($this->getIdName())."'
				placeholder='".$this->placeholder()."'
				value='".$this->defaultValue()."'
				type='text'
				class='acpt-country ".$this->cssClass()."'
				".$this->required()."
				".$this->appendDataValidateAndConditionalRenderingAttributes()."
				".$this->appendMaxLengthAndMinLength($max, $min)."
			/>";

		if($this->fieldModel->getMetaField() !== null){
			return (new AfterAndBeforeFieldGenerator())->generate($this->fieldModel->getMetaField(), $field);
		}

		return $field;
	}

	/**
	 * @inheritDoc
	 */
	public function enqueueFieldAssets()
	{
	    wp_enqueue_script( 'countrySelect-js', plugins_url( 'advanced-custom-post-type/assets/vendor/countrySelect/js/countrySelect.min.js'), [], '2.1.1', true);
	    wp_enqueue_style( 'countrySelect-css', plugins_url( 'advanced-custom-post-type/assets/vendor/countrySelect/css/countrySelect.min.css'), [], '2.1.1', 'all');
	}
}
