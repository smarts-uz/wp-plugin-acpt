<?php

namespace ACPT\Integrations\GenerateBlocks\Provider\Fields;

use ACPT\Utils\Wordpress\WPUtils;

class EditorField extends AbstractField
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
        $content = WPUtils::renderShortCode($rawValue);

        if($content === null){
            return null;
        }

        $replacementMap = [
                '<p>['    => '[',
                ']</p>'   => ']',
                ']<br />' => ']'
        ];

        return strtr( $content, $replacementMap );
    }
}