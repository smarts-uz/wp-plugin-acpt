<?php

namespace ACPT\Integrations\GenerateBlocks\Provider\Fields;

class EmailField extends AbstractField
{
    /**
     * @inheritDoc
     */
    protected function options(): array
    {
        return [
            'render' => [
                'type'    => 'select',
                'label'   => __( 'Render as', ACPT_PLUGIN_NAME ),
                'default' => 'text',
                'options' => $this->renderOptions(),
            ],
        ];
    }

    /**
     * @inheritDoc
     */
    protected function render($rawValue, $options = [])
    {
        if(isset($options['render']) and $options['render'] === "html"){
            return '<a href="mailto:' . sanitize_email($rawValue) . '">' . $rawValue . '</a>';
        }

        return $rawValue;
    }
}