<?php

namespace ACPT\Integrations\SlimSeo\Provider\Fields;

class DateRange extends Base
{
    /**
     * @return string|null
     */
    public function getValue()
    {
        if(is_array($this->value) and count($this->value) === 2){
            $value = $this->value[0]. " - " . $this->value[1];

            return wp_strip_all_tags($value);
        }

        return '';
    }
}
