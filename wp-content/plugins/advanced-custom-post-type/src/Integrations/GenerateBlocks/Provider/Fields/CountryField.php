<?php

namespace ACPT\Integrations\GenerateBlocks\Provider\Fields;

use ACPT\Utils\PHP\Country;

class CountryField extends DateField
{
    /**
     * @inheritDoc
     */
    protected function options(): array
    {
        return [
            'render' => [
                'type'    => 'select',
                'label'   => __( 'Render as', ACPT_PLUGIN_NAME ),
                'default' => 'country',
                'options' => $this->countryFormatOptions(),
            ],
        ];
    }

    /**
     * @inheritDoc
     */
    protected function render($rawValue, $options = [])
    {
        if(!is_array($rawValue)){
            return null;
        }

        if(!isset($rawValue['value'])){
            return null;
        }

        $countryIsoCode = $rawValue['country'];

        if(empty($countryIsoCode)){
            return null;
        }

        $render = $options['render'] ?? 'country';

        if($render === 'flag' and !empty($countryIsoCode)){
            return Country::getFlag($countryIsoCode);
        }

        if($render === 'full' and !empty($countryIsoCode)){
            return Country::fullFormat($countryIsoCode, $rawValue['value']);
        }

        return $rawValue['value'];
    }
}