<?php

namespace ACPT\Integrations\ElementorPro\Tags;

use ACPT\Utils\Wordpress\WPAttachment;
use Elementor\Modules\DynamicTags\Module;

class ACPTImageTag extends ACPTAbstractDataTag
{
	/**
	 * @inheritDoc
	 */
	public function get_categories()
	{
		return [
			Module::IMAGE_CATEGORY,
		];
	}

	/**
	 * @inheritDoc
	 */
	public function get_name()
	{
		return 'acpt-image';
	}

	/**
	 * @inheritDoc
	 */
	public function get_title()
	{
		return esc_html__( "ACPT Image field", ACPT_PLUGIN_NAME );
	}

	/**
	 * @param array $options
	 *
	 * @return array
	 */
	public function get_value( array $options = array() )
	{
		$field = $this->extractField();

		if(!empty($field)){
            $rawData = $this->getRawData();

            if($rawData instanceof WPAttachment){
                return [
                    'id' => $rawData->getId(),
                    'url' => $rawData->getSrc(),
                ];
            }

            return $this->emptyImage();
        }

		return $this->emptyImage();
	}

	/**
	 * @return array
	 */
	private function emptyImage()
	{
		return [
			'id' => 0,
			'url' => null,
		];
	}
}