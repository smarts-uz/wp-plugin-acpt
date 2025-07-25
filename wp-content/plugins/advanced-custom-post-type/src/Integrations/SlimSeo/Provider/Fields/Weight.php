<?php

namespace ACPT\Integrations\SlimSeo\Provider\Fields;

class Weight extends Base
{
    /**
     * @return string|null
     */
    public function getValue()
    {
        if(is_array($this->value) and isset($this->value['weight']) and isset($this->value['unit'])){
            $value = $this->value['weight'] . ' ' . $this->value['unit'];

            return wp_strip_all_tags($value);
        }

        return '';
    }
}
