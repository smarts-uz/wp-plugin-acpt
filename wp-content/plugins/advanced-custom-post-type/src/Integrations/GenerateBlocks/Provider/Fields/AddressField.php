<?php

namespace ACPT\Integrations\GenerateBlocks\Provider\Fields;

class AddressField extends AbstractField
{
    /**
     * @inheritDoc
     */
    protected function options(): array
    {
        return [
                'format' => [
                        'type'    => 'select',
                        'label'   => __( 'Format', ACPT_PLUGIN_NAME ),
                        'default' => 'address',
                        'options' => $this->addressFormatOptions(),
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

        $format = $options['format'] ?? "address";

        switch ($format){
            case "city":
                if(!isset($rawValue['city'])){
                    return null;
                }

                return $rawValue['city'];

            case "country":
                if(!isset($rawValue['country'])){
                    return null;
                }

                return $rawValue['country'];

            case "coordinates":
                if(!isset($rawValue['lat'])){
                    return null;
                }

                if(!isset($rawValue['lng'])){
                    return null;
                }

                return $rawValue['lat'].", ".$rawValue['lng'];

            default:
            case "address":
                if(!isset($rawValue['address'])){
                    return null;
                }

                return $rawValue['address'];
        }

        return null;
    }
}