<?php

namespace ACPT\Core\Generators\Form\Fields;

class PostDateField extends AbstractField
{
	/**
	 * @inheritDoc
	 */
	public function render()
	{
		$default = ($this->defaultValue() !== null) ? $this->defaultValue() : date("Y-m-d");

		return "
			<input
			    ".$this->disabled()."
				id='".esc_attr($this->getIdName())."'
				name='".esc_attr($this->getIdName())."'
				placeholder='".$this->placeholder()."'
				value='".$default."'
				type='date'
				class='".$this->cssClass()."'
				".$this->required()."
				".$this->appendDataValidateAndConditionalRenderingAttributes()."
			/>";
	}

	/**
	 * @inheritDoc
	 */
	public function enqueueFieldAssets() {
		// TODO: Implement enqueueFieldAssets() method.
	}
}
