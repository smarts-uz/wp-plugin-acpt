<?php

namespace ACPT\Integrations\GenerateBlocks\Provider\Fields;

use ACPT\Core\Helper\Currencies;

class CurrencyField extends AbstractField
{
    /**
     * @inheritDoc
     */
    protected function options(): array
    {
        return [
            'uom_position' => [
                'type'    => 'select',
                'label'   => __( 'UOM position', ACPT_PLUGIN_NAME ),
                'default' => 'after',
                'options' => $this->uomPositionOptions(),
            ],
            'raw' => [
                'type'  => 'checkbox',
                'label' => __( 'Display as raw number', ACPT_PLUGIN_NAME ),
                'help'  => __( 'Display the raw number without any formatting.', ACPT_PLUGIN_NAME ),
            ],
            'decimals' => [
                'type'  => 'number',
                'default' => "2",
                'min' => "0",
                'step' => "1",
                'label' => __( 'Number of decimals', ACPT_PLUGIN_NAME ),
                'help'  => __( 'Sets the number of decimal digits. Default: 2' ),
            ],
            'decimal_separator' => [
                'type'  => 'text',
                'default' => ".",
                'label' => __( 'Decimal separator', ACPT_PLUGIN_NAME ),
                'help'  => __( 'Sets the separator for the decimal point.' ),
            ],
            'thousands_separator' => [
                'type'  => 'text',
                'default' => ',',
                'label' => __( 'Thousands separator', ACPT_PLUGIN_NAME ),
                'help'  => __( 'Sets the thousands separator.' ),
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

        if(!isset($rawValue['currency'])){
            return null;
        }

        if(!isset($rawValue['currency']['symbol'])){
            return null;
        }

        if(!isset(Currencies::getList()[$rawValue['currency']]['symbol'])){
            return null;
        }

        if(isset($options['raw']) and $options['raw'] == 1){
            $value = $rawValue['value'];
        } else {
            $decimals = $options['decimals'] ?? 2;
            $decimal_separator = $options['decimal_separator'] ?? ".";
            $thousands_separator = $options['thousands_separator'] ?? ',';
            $value = number_format($rawValue['value'], $decimals, $decimal_separator, $thousands_separator);
        }

        $symbol = $rawValue['currency']['symbol'];
        $position = $options['uom_position'];

        if($position === "after"){
            return $value . " " . $symbol;
        }

        if($position === "before"){
            return $symbol . " " . $value;
        }

        return $value;
    }
}