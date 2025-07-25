<?php

namespace ACPT\Integrations\SlimSeo\Provider\Fields;

class Address extends Base
{
    /**
     * @return array
     */
    public function getValue()
    {
        if(is_array($this->value) and isset($this->value['address'])){
            return $this->value;
        }

        return [];
    }
}
