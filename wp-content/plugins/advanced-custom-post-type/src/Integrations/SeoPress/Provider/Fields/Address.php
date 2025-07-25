<?php

namespace ACPT\Integrations\SeoPress\Provider\Fields;

class Address extends Base
{
    /**
     * @return string|null
     */
    public function getValue()
    {
        if(is_array($this->value) and isset($this->value['address'])){
            return wp_strip_all_tags($this->value['address']);
        }

        return '';
    }
}
