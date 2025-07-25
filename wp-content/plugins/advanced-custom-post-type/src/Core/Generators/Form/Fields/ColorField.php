<?php

namespace ACPT\Core\Generators\Form\Fields;

class ColorField extends AbstractField
{
	/**
	 * @inheritDoc
	 */
	public function render()
	{
		return "
			<div class='acpt-color-picker'>
			    <input
                    ".$this->disabled()."
                    id='".esc_attr($this->getIdName())."'
                    name='".esc_attr($this->getIdName())."'
                    placeholder='".$this->placeholder()."'
                    value='".$this->defaultValue()."'
                    type='color'
                    class='".$this->cssClass()."'
                    ".$this->required()."
                    ".$this->appendDataValidateAndConditionalRenderingAttributes()."
			    />
			    <span class='color_val'>".$this->defaultValue()."</span>
			</div>";
	}

	/**
	 * @inheritDoc
	 */
	public function enqueueFieldAssets() {
		// TODO: Implement enqueueFieldAssets() method.
	}
}