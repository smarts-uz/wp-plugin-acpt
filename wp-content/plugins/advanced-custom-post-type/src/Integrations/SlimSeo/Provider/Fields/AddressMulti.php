<?php

namespace ACPT\Integrations\SlimSeo\Provider\Fields;

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
                    $addresses[] = $address;
                }
            }

            return $addresses;
        }

        return [];
    }
}
