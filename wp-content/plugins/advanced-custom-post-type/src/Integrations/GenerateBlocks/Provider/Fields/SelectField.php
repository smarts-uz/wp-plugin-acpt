<?php

namespace ACPT\Integrations\GenerateBlocks\Provider\Fields;

class SelectField extends AbstractField
{
    /**
     * @inheritDoc
     */
    protected function options(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    protected function render($rawValue, $options = [])
    {
        return $rawValue;
    }
}