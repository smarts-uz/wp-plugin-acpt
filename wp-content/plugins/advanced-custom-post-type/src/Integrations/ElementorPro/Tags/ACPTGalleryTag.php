<?php

namespace ACPT\Integrations\ElementorPro\Tags;

use ACPT\Utils\Wordpress\WPAttachment;
use Elementor\Modules\DynamicTags\Module;

class ACPTGalleryTag extends ACPTAbstractDataTag
{
	/**
	 * @inheritDoc
	 */
	public function get_categories()
	{
		return [
			Module::GALLERY_CATEGORY,
		];
	}

	/**
	 * @inheritDoc
	 */
	public function get_name()
	{
		return 'acpt-gallery';
	}

	/**
	 * @inheritDoc
	 */
	public function get_title()
	{
		return esc_html__( "ACPT Gallery field", ACPT_PLUGIN_NAME );
	}

	public function get_value( array $options = array() )
	{
		$field = $this->extractField();
		$gallery = [];

		if(!empty($field)){
            $rawData = $this->getRawData();

            if(!empty($rawData) and is_array($rawData)){

                $sort = $field['sort'] ?? 'asc';

                if($sort === 'desc'){
                    $rawData = array_reverse($rawData);
                }

                if($sort === 'rand'){
                    shuffle($rawData);
                }

                /** @var WPAttachment $image */
                foreach ( $rawData as $image ) {
                    $gallery[] = [
                        'id' => $image->getId(),
                        'url' => $image->getSrc(),
                    ];
                }
            }

            return $gallery;
        }

        return $gallery;
	}
}
