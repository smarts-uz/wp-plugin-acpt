<?php

namespace ACPT\Core\Generators\Form\Fields;

use ACPT\Core\Generators\Meta\AfterAndBeforeFieldGenerator;

class UrlField extends AbstractField
{
	/**
	 * @inheritDoc
	 */
	public function render()
	{
		$extra = $this->fieldModel->getExtra();
		$hideLabel = (isset($extra['hideLabel'])) ? $extra['hideLabel'] : false;
		$labelPlaceholder = (isset($extra['labelPlaceholder'])) ? $extra['labelPlaceholder'] : false;

        if($this->isNested and $this->fieldModel->getMetaField() !== null){
            $min = $this->fieldModel->getMetaField()->getAdvancedOption("min") ?? null;
            $max = $this->fieldModel->getMetaField()->getAdvancedOption("max") ?? null;
        } else {
            $min = (!empty($this->fieldModel->getExtra()['min'])) ? esc_attr($this->fieldModel->getExtra()['min']) : null;
            $max = (!empty($this->fieldModel->getExtra()['max'])) ? esc_attr($this->fieldModel->getExtra()['max']) : null;
        }

		if($hideLabel){
			return "
				<input
				    ".$this->disabled()."
					id='".esc_attr($this->getIdName())."'
					name='".esc_attr($this->getIdName())."'
					placeholder='".$this->placeholder()."'
					value='".$this->defaultUrlValue()."'
					type='url'
					class='".$this->cssClass()."'
					".$this->required()."
					".$this->appendDataValidateAndConditionalRenderingAttributes()."
					".$this->appendMaxLengthAndMinLength($max, $min)."
				/>
			";
		}

		$label = "
			<input
			        ".$this->disabled()."
					id='".esc_attr($this->getIdName())."_label'
					name='".esc_attr($this->getIdName())."_label'
					placeholder='".$labelPlaceholder."'
					value='".$this->defaultUrlLabel()."'
					type='text'
					class='".$this->cssClass()."'
					".$this->required()."
					".$this->appendDataValidateAndConditionalRenderingAttributes()."
					".$this->appendMaxLengthAndMinLength($max, $min)."
				/>";

		if($this->fieldModel->getMetaField() !== null){
			$label = (new AfterAndBeforeFieldGenerator())->generate($this->fieldModel->getMetaField(), $label, "width: 50%");
		}

		return "
			<div class='acpt-form-inline'>
				<input
				    ".$this->disabled()."
					style='width: 50%'
					id='".esc_attr($this->getIdName())."'
					name='".esc_attr($this->getIdName())."'
					placeholder='".$this->placeholder()."'
					value='".$this->defaultUrlValue()."'
					type='url'
					class='".$this->cssClass()."'
					".$this->required()."
					".$this->appendDataValidateAndConditionalRenderingAttributes()."
					".$this->appendMaxLengthAndMinLength($max, $min)."
				/>
				".$label."
			</div>
			";
	}

    /**
     * @return string
     */
    private function defaultUrlValue()
    {
        $defaultValue = $this->defaultValue();

        if(is_scalar($defaultValue)){
            return $defaultValue;
        }

        if(is_array($defaultValue) and isset($defaultValue['url'])){
            return $defaultValue['url'];
        }

        return null;
    }

    /**
     * @return string
     */
    private function defaultUrlLabel()
    {
        $savedLabel = $this->defaultExtraValue("label");

        if(!empty($savedLabel)){
            return $savedLabel;
        }

        if(isset($this->fieldModel->getExtra()['defaultValue']) and isset($this->fieldModel->getExtra()['defaultValue']['urlLabel'])){
            return $this->fieldModel->getExtra()['defaultValue']['urlLabel'];
        }

        return (isset($this->fieldModel->getExtra()['labelDefaultValue'])) ? $this->fieldModel->getExtra()['labelDefaultValue'] : '';
    }

	/**
	 * @inheritDoc
	 */
	public function enqueueFieldAssets() {
		// TODO: Implement enqueueFieldAssets() method.
	}
}
