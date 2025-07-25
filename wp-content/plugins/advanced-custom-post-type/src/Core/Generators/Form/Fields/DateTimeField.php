<?php

namespace ACPT\Core\Generators\Form\Fields;

use ACPT\Core\Generators\Meta\AfterAndBeforeFieldGenerator;

class DateTimeField extends AbstractField
{
	/**
	 * @inheritDoc
	 */
	public function render()
	{
        if($this->isNested and $this->fieldModel->getMetaField() !== null){
            $min = $this->fieldModel->getMetaField()->getAdvancedOption("min") ?? null;
            $max = $this->fieldModel->getMetaField()->getAdvancedOption("max") ?? null;
            $step = $this->fieldModel->getMetaField()->getAdvancedOption("step") ?? null;
        } else {
            $min = (!empty($this->fieldModel->getExtra()['min'])) ? esc_attr($this->fieldModel->getExtra()['min']) : null;
            $max = (!empty($this->fieldModel->getExtra()['max'])) ? esc_attr($this->fieldModel->getExtra()['max']) : null;
            $step = (!empty($this->fieldModel->getExtra()['step'])) ? esc_attr($this->fieldModel->getExtra()['step']) : null;
        }

        $field = "
			<input
			    ".$this->disabled()."
				id='".esc_attr($this->getIdName())."'
				name='".esc_attr($this->getIdName())."'
				placeholder='".$this->placeholder()."'
				value='".$this->defaultValue()."'
				type='datetime-local'
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
