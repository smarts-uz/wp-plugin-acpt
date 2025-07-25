<?php

namespace ACPT\Core\Shortcodes\ACPT\Fields;

use ACPT\Utils\Wordpress\WPUtils;

class TextareaField extends AbstractField
{
    public function render()
    {
        if(!$this->isFieldVisible()){
            return null;
        }

	    $rawData = $this->fetchRawData();

	    if(!isset($rawData['value'])){
		    return null;
	    }

	    return $this->addBeforeAndAfter($this->renderTextarea($rawData['value']));
    }

	/**
	 * @param $data
	 *
	 * @return string
	 */
    private function renderTextarea($data)
    {
    	return WPUtils::renderShortCode($data, true);
    }
}