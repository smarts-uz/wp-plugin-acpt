<?php

namespace ACPT\Integrations\GenerateBlocks\Provider\Fields;

class DateField extends AbstractField
{
    /**
     * @inheritDoc
     */
    protected function options(): array
    {
        return [
            'format' => [
                'type'    => 'select',
                'label'   => __( 'Date format', ACPT_PLUGIN_NAME ),
                'default' => 'text',
                'options' => $this->dateOptions(),
            ],
        ];
    }

    /**
     * @inheritDoc
     */
    protected function render($rawValue, $options = [])
    {
        try {
            $dateFormat = $this->getDefaultDateFormat($options['format']);

            if(!is_string($rawValue)){
                return null;
            }

            $date = new \DateTime($rawValue);

            return $this->formatDate($dateFormat, $date);
        } catch (\Exception $exception) {
            return null;
        }
    }

    /**
     * @param null $format
     *
     * @return mixed|string|void|null
     */
    protected function getDefaultDateFormat($format = null)
    {
        if($format !== null){
            return $format;
        }

        if($this->fieldModel !== null and $this->fieldModel->getAdvancedOption('date_format') !== null){
            return $this->fieldModel->getAdvancedOption('date_format');
        }

        if(!empty(get_option('date_format'))){
            return get_option('date_format');
        }

        return "Y-m-d";
    }

    /**
     * @param null $format
     *
     * @return mixed|string|void|null
     */
    protected function getDefaultTimeFormat($format = null)
    {
        if($format !== null){
            return $format;
        }

        if($this->fieldModel !== null and $this->fieldModel->getAdvancedOption('time_format') !== null){
            return $this->fieldModel->getAdvancedOption('time_format');
        }

        if(!empty(get_option('time_format'))){
            return get_option('time_format');
        }

        return "H:i";
    }

    /**
     * @param $format
     * @param \DateTime $date
     *
     * @return string
     */
    protected function formatDate($format, \DateTime $date)
    {
        return date_i18n($format, $date->getTimestamp());
    }
}