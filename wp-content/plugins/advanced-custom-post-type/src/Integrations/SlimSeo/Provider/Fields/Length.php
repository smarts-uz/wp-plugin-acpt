<?php

namespace ACPT\Integrations\SlimSeo\Provider\Fields;

class Length extends Base
{
    /**
     * @return string|null
     */
    public function getValue()
    {
        if(is_array($this->value) and isset($this->value['length']) and isset($this->value['unit'])){
            $value = $this->value['length'] . ' ' . $this->value['unit'];

            return wp_strip_all_tags($value);
        }

        return '';
    }
}
