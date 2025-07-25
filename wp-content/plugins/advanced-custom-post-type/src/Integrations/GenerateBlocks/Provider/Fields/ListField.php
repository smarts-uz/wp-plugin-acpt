<?php

namespace ACPT\Integrations\GenerateBlocks\Provider\Fields;

use ACPT\Utils\Wordpress\WPUtils;

class ListField extends AbstractField
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
                'default' => 'ul',
                'options' => $this->listRenderingOptions(),
            ],
            'separator' => [
                'type'  => 'text',
                'default' => ',',
                'label' => __( 'List item separator', ACPT_PLUGIN_NAME ),
                'help'  => __( 'Sets the list separator if you are rendering it as text.' ),
            ],
        ];
    }

    /**
     * @inheritDoc
     */
    protected function render($rawValue, $options = [])
    {
        if(!is_array($rawValue)){
            return null;
        }

        $render = $options['render'] ?? "ul";
        $separator = $options['separator'] ?? ",";

        if($render === "ul"){
            $return = '<ul>';

            foreach ($rawValue as $value){
                $return = '<li>'.$value.'</li>';
            }

            $return .= '</ul>';

            return $return;
        }

        if($render === "ol"){
            $return = '<ol>';

            foreach ($rawValue as $value){
                $return = '<li>'.$value.'</li>';
            }

            $return .= '</ol>';

            return $return;
        }

        return implode($rawValue, $separator);
    }
}