<?php

namespace ACPT\Integrations\SlimSeo\Provider\Fields;

class ArrayValues extends Base
{
    /**
     * @return array|null
     */
    public function getValue()
    {
        if(is_array($this->value) and !empty($this->value)){
            return $this->value;
        }

        return [];
    }
}
