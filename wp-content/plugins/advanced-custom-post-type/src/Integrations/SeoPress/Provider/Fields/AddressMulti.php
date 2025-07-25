<?php

namespace ACPT\Integrations\SeoPress\Provider\Fields;

class AddressMulti extends Base
{
    /**
     * @return array|null
     */
    public function getValue()
    {
        if(is_array($this->value) and !empty($this->value)){
            $addresses = [];
            foreach ($this->value as $address){
                if(is_array($address) and isset($address['address'])){
                    $addresses[] = wp_strip_all_tags($address['address']);
                }
            }

            return $addresses;
        }

        return [];
    }
}
