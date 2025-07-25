<?php

namespace ACPT\Integrations\SlimSeo\Provider\Fields;

class Base
{
    protected $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @return string|null
     */
    public function getValue()
    {
        if(!is_string($this->value)){
            return null;
        }

        return wp_strip_all_tags( $this->value, true );
    }
}
