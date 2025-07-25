<?php

namespace ACPT\Core\Generators\Form\Fields;

use ACPT\Core\Generators\Meta\AfterAndBeforeFieldGenerator;

class NumberField extends AbstractField
{
	/**
	 * @inheritDoc
	 */
	public function render()
	{
        if($this->isNested and $this->fieldModel->getMetaField() !== null){
            $min = $this->fieldModel->getMetaField()->getAdvancedOption("min") ?? 1;
            $max = $this->fieldModel->getMetaField()->getAdvancedOption("max") ?? 100;
            $step = $this->fieldModel->getMetaField()->getAdvancedOption("step") ?? 1;
        } else {
            $min = (!empty($this->fieldModel->getExtra()['min'])) ? esc_attr($this->fieldModel->getExtra()['min']) : 1;
            $max = (!empty($this->fieldModel->getExtra()['max'])) ? esc_attr($this->fieldModel->getExtra()['max']) : 100;
            $step = (!empty($this->fieldModel->getExtra()['step'])) ? esc_attr($this->fieldModel->getExtra()['step']) : 1;
        }

		$field = "
			<input
			    ".$this->disabled()."
				id='".esc_attr($this->getIdName())."'
				name='".esc_attr($this->getIdName())."'
				placeholder='".$this->placeholder()."'
				value='".$this->defaultValue()."'
				type='number'
				class='".$this->cssClass()."'
				".$this->required()."
				".$this->appendDataValidateAndConditionalRenderingAttributes()."
				".$this->appendMaxMinAndStep($max, $min, $step)."
			/>";

		if($this->fieldModel->getMetaField() !== null){
			return (new AfterAndBeforeFieldGenerator())->generate($this->fieldModel->getMetaField(), $field);
		}

		return $field;
	}

	/**
	 * @inheritDoc
	 */
	public function enqueueFieldAssets() {
		// TODO: Implement enqueueFieldAssets() method.
	}
}
