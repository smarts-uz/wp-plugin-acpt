<?php

namespace ACPT\Integrations\GenerateBlocks\Provider\Fields;

class NumberField extends AbstractField
{
    /**
     * @inheritDoc
     */
    protected function options(): array
    {
        return [
            'raw' => [
                'type'  => 'checkbox',
                'label' => __( 'Display as raw number', ACPT_PLUGIN_NAME ),
                'help'  => __( 'Display the raw number without any formatting.', ACPT_PLUGIN_NAME ),
            ],
            'decimals' => [
                'type'  => 'number',
                'default' => "0",
                'min' => "0",
                'step' => "1",
                'label' => __( 'Number of decimals', ACPT_PLUGIN_NAME ),
                'help'  => __( 'Sets the number of decimal digits. Default: 0' ),
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
        if(isset($options['raw']) and $options['raw'] == 1){
            return $rawValue;
        }

        $decimals = $options['decimals'] ?? 0;
        $decimal_separator = $options['decimal_separator'] ?? ".";
        $thousands_separator = $options['thousands_separator'] ?? ',';

        return number_format($rawValue, $decimals, $decimal_separator, $thousands_separator);
    }
}