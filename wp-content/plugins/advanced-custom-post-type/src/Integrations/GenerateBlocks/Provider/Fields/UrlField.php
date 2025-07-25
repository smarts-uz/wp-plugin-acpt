<?php

namespace ACPT\Integrations\GenerateBlocks\Provider\Fields;

class UrlField extends AbstractField
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
                'default' => 'url',
                'options' => $this->urlRenderOptions(),
            ],
            'target' => [
                'type'    => 'select',
                'label'   => __( 'Link target', ACPT_PLUGIN_NAME ),
                'default' => '_blank',
                'options' => $this->targetOptions(),
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

        if(!isset($rawValue['value'])){
            return null;
        }

        $label = isset($rawValue['label']) ? $rawValue['label'] : $rawValue['value'];
        $render = $options['render'] ?? "html";
        $target = $options['target'] ?? "_blank";

        if($render === "label"){
            return $label;
        }

        if($render === "url"){
            return $rawValue['value'];
        }

        return '<a href="'.$rawValue['value'].'" target="'.$target.'">'.$label.'</a>';
    }
}