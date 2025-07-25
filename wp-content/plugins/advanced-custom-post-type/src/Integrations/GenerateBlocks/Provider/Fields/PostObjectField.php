<?php

namespace ACPT\Integrations\GenerateBlocks\Provider\Fields;

class PostObjectField extends AbstractField
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
                'default' => 'title',
                'options' => $this->objectRenderingOptions("post"),
            ]
        ];
    }

    /**
     * @inheritDoc
     */
    protected function render($rawValue, $options = [])
    {
        if(!is_numeric($rawValue)){
            return null;
        }

        $rawValue = (int)$rawValue;
        $render = $options['render'] ?? "title";

        if($render === "id"){
            return $rawValue;
        }

        if($render === "link"){
            $title = get_the_title($rawValue);
            $link = get_the_permalink($rawValue);

            return '<a href="'.$link.'" target="_blank">'.$title.'</a>';
        }

        return get_the_title($rawValue);
    }
}