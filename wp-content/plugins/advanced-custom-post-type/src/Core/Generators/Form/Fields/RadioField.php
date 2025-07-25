<?php

namespace ACPT\Core\Generators\Form\Fields;

use ACPT\Utils\Wordpress\Translator;

class RadioField extends AbstractField
{
	/**
	 * @inheritDoc
	 */
	public function render()
	{
		$value = $this->defaultValue();

		if(!is_string($value)){
            $value = null;
        }

		$options = (!empty($this->fieldModel->getExtra()['options'])) ? $this->fieldModel->getExtra()['options'] : [];

		$field = '';

        if($this->fieldModel->getMetaField() !== null and empty($this->fieldModel->getMetaField()->getAdvancedOption('hide_blank_radio'))){
            $field .= '
                <input 
                     '.$this->disabled().'
                    name="'.esc_attr($this->getIdName()).'" 
                    id="'.esc_attr($this->getIdName()).'_blank" 
                    type="radio"
                    value="" 
                    '.(empty($value) ? "checked" : "").'
			        '.$this->required().'
			        '.$this->appendDataValidateAndConditionalRenderingAttributes().'
                />
                <label for="'.esc_attr($this->getIdName()).'_blank">'.Translator::translate('No choice').'</label>
           ';
        }

		foreach ($options as $index => $option){
			$field .= '
				<input 
				    '.$this->disabled().'
					id="'.esc_attr($this->getIdName()).'_'.$index.'"
					name="'.esc_attr($this->getIdName()).'"
					type="radio" 
			        value="'.esc_attr($option['value']).'"
			        '.($option['value'] == $value ? "checked" : "").'
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
