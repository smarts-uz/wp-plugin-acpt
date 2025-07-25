<?php

namespace ACPT\Core\Generators\Form\Fields;

class CheckboxField extends AbstractField
{
	/**
	 * @inheritDoc
	 */
	public function render()
	{
		$value = $this->defaultValue();

		if(!is_array($value)){
            $value = [];
        }

		$options = (!empty($this->fieldModel->getExtra()['options'])) ? $this->fieldModel->getExtra()['options'] : [];

		$field = '';

		foreach ($options as $index => $option){
			$field .= '
				<input 
				    '.$this->disabled().'
					id="'.esc_attr($this->getIdName()).'_'.$index.'"
					name="'.esc_attr($this->getIdName()).'[]"
					type="checkbox" 
			        value="'.esc_attr($option['value']).'"
			        '.(in_array($option['value'], $value) ? "checked" : "").'
			        '.$this->required().'
			        '.$this->appendDataValidateAndConditionalRenderingAttributes().'
		        />
			    <label class="checkbox-label" for="'.esc_attr($this->getIdName()).'_'.$index.'">
			    	'.esc_attr($option['label']).'    
				</label>';
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
