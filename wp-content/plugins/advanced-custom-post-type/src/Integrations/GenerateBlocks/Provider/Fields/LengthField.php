<?php

namespace ACPT\Integrations\GenerateBlocks\Provider\Fields;

use ACPT\Core\Helper\Lengths;

class LengthField extends CurrencyField
{
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

        if(!isset($rawValue['length'])){
            return null;
        }

        if(!isset($rawValue['length']['symbol'])){
            return null;
        }

        if(!isset(Lengths::getList()[$rawValue['length']]['symbol'])){
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

        $symbol = $rawValue['length']['symbol'];
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