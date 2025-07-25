<?php

namespace ACPT\Core\Generators\Form\Fields;

class RangeField extends AbstractField
{
	/**
	 * @inheritDoc
	 */
	public function render()
	{
		$min = (!empty($this->fieldModel->getExtra()['min'])) ? esc_attr($this->fieldModel->getExtra()['min']) : 1;
		$max = (!empty($this->fieldModel->getExtra()['max'])) ? esc_attr($this->fieldModel->getExtra()['max']) : 100;
		$step = (!empty($this->fieldModel->getExtra()['step'])) ? esc_attr($this->fieldModel->getExtra()['step']) : 1;

		return "
			<input
			    ".$this->disabled()."
				id='".esc_attr($this->getIdName())."'
				name='".esc_attr($this->getIdName())."'
				placeholder='".$this->placeholder()."'
				value='".$this->defaultValue()."'
				type='range'
				class='".$this->cssClass()."'
				".$this->required()."
				".$this->appendMaxMinAndStep($max, $min, $step)."
			/>";
	}

	/**
	 * @inheritDoc
	 */
	public function enqueueFieldAssets() {
		// TODO: Implement enqueueFieldAssets() method.
	}
}
