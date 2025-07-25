<?php

namespace ACPT\Core\Shortcodes\ACPT\Fields;

use ACPT\Utils\Wordpress\WPAttachment;

class ImageField extends AbstractField
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

	    $wpAttachment = $this->getAttachment($rawData);

        if(empty($wpAttachment)){
            return null;
        }

        return $this->addBeforeAndAfter($this->renderImage($wpAttachment));
    }

    /**
     * @param WPAttachment $wpAttachment
     * @return int|string|null
     */
    private function renderImage(WPAttachment $wpAttachment)
    {
	    if($wpAttachment->isEmpty()){
	    	return null;
	    }

	    if($this->payload->preview){
	    	return $this->addBeforeAndAfter('<img style="border: 1px solid #c3c4c7; object-fit: fill;" src="'. esc_url($wpAttachment->getSrc('thumbnail')).'" width="'.esc_attr(80).'" height="'.esc_attr(60).'" title="'.$wpAttachment->getTitle().'" alt="'.$wpAttachment->getAlt().'" />');
	    }

        $render = $this->metaBoxFieldModel->getAdvancedOption("render") ?? "html";

        if($render === "id"){
            return $wpAttachment->getId();
        }

        if($render === "url"){
            return $wpAttachment->getSrc();
        }

	    $width = ($this->payload->width !== null) ? $this->payload->width : '100%';
	    $height = ($this->payload->height !== null) ? $this->payload->height : null;

	    return $this->addBeforeAndAfter('<img src="'.$wpAttachment->getSrc().'" width="'.esc_attr($width).'" height="'.esc_attr($height).'" title="'.$wpAttachment->getTitle().'" alt="'.$wpAttachment->getAlt().'" />');
    }
}