<?php

namespace ACPT\Integrations\GenerateBlocks\Provider\Fields;

class TimeField extends DateField
{
    /**
     * @inheritDoc
     */
    protected function options(): array
    {
        return [
            'format' => [
                'type'    => 'select',
                'label'   => __( 'Time format', ACPT_PLUGIN_NAME ),
                'default' => 'text',
                'options' => $this->timeOptions(),
            ],
        ];
    }

    /**
     * @inheritDoc
     */
    protected function render($rawValue, $options = [])
    {
        try {
            $timeFormat = $this->getDefaultTimeFormat($options['format']);

            if(!is_string($rawValue)){
                return null;
            }

            $date = new \DateTime($rawValue);

            return $this->formatDate($timeFormat, $date);
        } catch (\Exception $exception) {
            return null;
        }
    }
}