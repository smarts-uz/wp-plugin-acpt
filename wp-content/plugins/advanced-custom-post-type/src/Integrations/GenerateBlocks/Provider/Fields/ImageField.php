<?php

namespace ACPT\Integrations\GenerateBlocks\Provider\Fields;

use ACPT\Utils\Wordpress\WPAttachment;

class ImageField extends AbstractField
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
        if(!is_numeric($rawValue)){
            return null;
        }

        $attachment = WPAttachment::fromId($rawValue);

        if($attachment->isEmpty()){
            return null;
        }

        return $attachment->getSrc();
    }
}