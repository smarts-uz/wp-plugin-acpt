<?php

namespace ACPT\Integrations\GenerateBlocks\Provider\Fields;

class HTMLField extends AbstractField
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