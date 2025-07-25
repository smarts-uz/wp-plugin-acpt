<?php

namespace ACPT\Integrations\ElementorPro;

use ACPT\Integrations\AbstractIntegration;
use ACPT\Integrations\ElementorPro\Constants\TagsConstants;
use Elementor\Core\DynamicTags\Manager as DynamicTagsManager;

class ACPT_Elementor_Pro extends AbstractIntegration
{
    /**
     * @inheritDoc
     */
    protected function name()
    {
        return "elementor_pro";
    }

	/**
	 * @inheritDoc
	 */
	protected function isActive()
	{
		$isActive = is_plugin_active( 'elementor-pro/elementor-pro.php' );

		if(!$isActive){
			return false;
		}

		return ACPT_ENABLE_META == 1 and $isActive;
	}

	/**
	 * @inheritDoc
	 */
	protected function runIntegration()
	{
		add_action( 'elementor/dynamic_tags/register', [$this, 'registerTags'] );
	}

	/**
	 * @param DynamicTagsManager $dynamic_tags_manager
	 */
	public function registerTags( DynamicTagsManager $dynamic_tags_manager )
	{
		$dynamic_tags_manager->register_group(
			TagsConstants::GROUP_NAME,
			[
				'title' => esc_html__( 'ACPT fields', ACPT_PLUGIN_NAME )
			]
		);

		$fields = DynamicDataProvider::getInstance()->getFields();

		if(!empty($fields)){
			foreach ($fields as $tag => $tags){
				$dynamic_tags_manager->register(new $tag());
			}
		}
	}
}