<?php

namespace ACPT\Integrations\Breakdance\Provider\Fields;

use ACPT\Utils\PHP\Phone;
use ACPT\Utils\Wordpress\Translator;
use Breakdance\DynamicData\StringData;

class ACPTPhoneField extends ACPTStringField
{
	/**
	 * @return array
	 */
	public function controls()
	{
		return [
            \Breakdance\Elements\control('format', 'Phone format', [
                'type' => 'dropdown',
                'layout' => 'vertical',
                'items' => [
                    ['text' => Translator::translate(Phone::FORMAT_E164), 'value' => Phone::FORMAT_E164],
                    ['text' => Translator::translate(Phone::FORMAT_INTERNATIONAL), 'value' => Phone::FORMAT_INTERNATIONAL],
                    ['text' => Translator::translate(Phone::FORMAT_NATIONAL), 'value' => Phone::FORMAT_NATIONAL],
                ],
            ]),
			\Breakdance\Elements\control('render', Translator::translate('Render as'), [
				'type' => 'dropdown',
				'layout' => 'vertical',
				'items' => [
					['text' => Translator::translate('Plain text'), 'value' => 'text'],
					['text' => Translator::translate('Link'), 'value' => 'link'],
				]
			]),
			\Breakdance\Elements\control('id', 'ID', [
				'type' => 'text',
				'layout' => 'vertical',
				'condition' => [
					'path' => 'attributes.render',
					'operand' => 'equals',
					'value' => 'link'
				]
			]),
			\Breakdance\Elements\control('classes',  Translator::translate('Classes (separated by space)'), [
				'type' => 'text',
				'layout' => 'vertical',
				'condition' => [
					'path' => 'attributes.render',
					'operand' => 'equals',
					'value' => 'link'
				]
			]),
			\Breakdance\Elements\control('target', 'Target link', [
				'type' => 'dropdown',
				'layout' => 'vertical',
				'items' => [
					['text' => Translator::translate('Opens in a new window or tab '), 'value' => '_blank'],
					['text' => Translator::translate('Opens in the full body of the window'), 'value' => '_self'],
					['text' => Translator::translate('Opens in the parent frame'), 'value' => '_parent'],
					['text' => Translator::translate('Opens in the same frame as it was clicked'), 'value' => '_top'],
				],
				'condition' => [
					'path' => 'attributes.render',
					'operand' => 'equals',
					'value' => 'link'
				]
			])
		];
	}

	/**
	 * @inheritDoc
	 */
	public function defaultAttributes()
	{
		return [
			'id' => '',
			'classes' => '',
			'render' => 'text',
			'target' => '_blank',
		];
	}

	/**
	 * @param mixed $attributes
	 *
	 * @return StringData
	 * @throws \Exception
	 */
	public function handler($attributes): StringData
	{
		$id = $attributes['id'] ?? '';
		$classes = $attributes['classes'] ?? '';
		$render = $attributes['render'] ?? null;
		$target = $attributes['target'] ?? '_blank';
		$format = $attributes['format'] ?? Phone::FORMAT_E164;
		$value = ACPTField::getValue($this->fieldModel, $attributes);

		if(!is_string($value) or $value === null){
			return StringData::emptyString();
		}

        $phone = Phone::format($value, null, $format);

		if($render === 'link'){
            $phone = '<a id="'.$id.'" class="'.$classes.'" href="'.Phone::format($value, null, Phone::FORMAT_RFC3966).'" target="'.$target.'">'.Phone::format($value, null, $format).'</a>';
		}

		return StringData::fromString($phone);
	}
}
