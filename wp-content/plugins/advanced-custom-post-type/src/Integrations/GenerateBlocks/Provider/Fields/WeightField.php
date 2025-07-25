<?php

namespace ACPT\Integrations\GenerateBlocks\Provider\Fields;

use ACPT\Core\Helper\Weights;

class WeightField extends CurrencyField
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

        if(!isset($rawValue['weight'])){
            return null;
        }

        if(!isset($rawValue['weight']['symbol'])){
            return null;
        }

        if(!isset(Weights::getList()[$rawValue['weight']]['symbol'])){
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

        $symbol = $rawValue['weight']['symbol'];
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