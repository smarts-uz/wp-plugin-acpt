<?php

namespace ACPT\Integrations\GenerateBlocks\Provider\Fields;

use ACPT\Utils\Wordpress\Users;

class UserField extends AbstractField
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
                'options' => $this->objectRenderingOptions("user"),
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

        $user = get_user($rawValue);

        if(!$user instanceof \WP_User){
            return null;
        }

        return Users::getUserLabel($user);
    }
}