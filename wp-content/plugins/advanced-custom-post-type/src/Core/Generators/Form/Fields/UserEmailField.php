<?php

namespace ACPT\Core\Generators\Form\Fields;

class UserEmailField extends AbstractField
{
	/**
	 * @inheritDoc
	 */
	public function render()
	{
        $min = (!empty($this->fieldModel->getExtra()['min'])) ? esc_attr($this->fieldModel->getExtra()['min']) : null;
        $max = (!empty($this->fieldModel->getExtra()['max'])) ? esc_attr($this->fieldModel->getExtra()['max']) : null;

        return "
			<input
			    ".$this->disabled()."
				id='".esc_attr($this->getIdName())."'
				name='".esc_attr($this->getIdName())."'
				placeholder='".$this->placeholder()."'
				value='".$this->defaultValue()."'
				type='email'
				class='".$this->cssClass()."'
				".$this->required()."
				".$this->appendDataValidateAndConditionalRenderingAttributes()."
				".$this->appendMaxLengthAndMinLength($max, $min)."
			/>";
	}

	/**
	 * @inheritDoc
	 */
	public function enqueueFieldAssets() {
		// TODO: Implement enqueueFieldAssets() method.
	}
}
