<?php

namespace ACPT\Integrations\GenerateBlocks\Provider\Fields;

class DateTimeField extends DateField
{
    /**
     * @inheritDoc
     */
    protected function options(): array
    {
        return [
            'date_format' => [
                'type'    => 'select',
                'label'   => __( 'Date format', ACPT_PLUGIN_NAME ),
                'default' => 'text',
                'options' => $this->dateOptions(),
            ],
            'time_format' => [
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
            $dateFormat = $this->getDefaultDateFormat();
            $timeFormat = $this->getDefaultTimeFormat();
            $dateTimeFormat = $dateFormat . ' ' . $timeFormat;

            if(!is_string($rawValue)){
                return null;
            }

            $date = new \DateTime($rawValue);

            return $this->formatDate($dateTimeFormat, $date);
        } catch (\Exception $exception) {
            return null;
        }
    }
}