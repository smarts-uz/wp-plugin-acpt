<?php

namespace ACPT\Integrations\GenerateBlocks\Provider\Fields;

class TermObjectField extends AbstractField
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
                'options' => $this->objectRenderingOptions("term"),
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

        $term = get_term($rawValue);

        if(!$term instanceof \WP_Term){
            return null;
        }

        if($render === "link"){
            $link = get_term_link($term);

            return '<a href="'.$link.'" target="_blank">'.$term->name.'</a>';
        }

        return $term->name;
    }
}