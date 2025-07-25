<?php

namespace ACPT\Core\Generators\Form\Fields;

class PostThumbnailField extends AbstractField
{
	/**
	 * @inheritDoc
	 */
	public function render()
	{
		$value = $this->defaultValue();

		$field = "
			<input
			    ".$this->disabled()."
				id='".esc_attr($this->getIdName())."'
				name='".esc_attr($this->getIdName())."'
				accept='image/*'
				placeholder='".$this->placeholder()."'
				type='file'
				class='".$this->cssClass()."'
				".$this->required()."
				".$this->appendDataValidateAndConditionalRenderingAttributes()."
			/>";

		if(empty($value)){
			return $field;
		}

		return "<div class='acpt-form-inline'>
			<img src='".$value."' class='acpt-thumbnail'/>
			".$field."
			</div>";
	}

	/**
	 * @inheritDoc
	 */
	public function enqueueFieldAssets() {
		// TODO: Implement enqueueFieldAssets() method.
	}
}
