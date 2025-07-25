<?php

namespace ACPT\Integrations\GenerateBlocks\Provider\Fields;

use ACPT\Utils\Wordpress\WPUtils;

class TextField extends AbstractField
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
        return WPUtils::renderShortCode($rawValue);
    }
}