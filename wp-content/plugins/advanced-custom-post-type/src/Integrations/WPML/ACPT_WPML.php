<?php

namespace ACPT\Integrations\WPML;

use ACPT\Integrations\AbstractIntegration;
use ACPT\Integrations\Polylang\Helper\PolylangChecker;
use ACPT\Integrations\WPML\Helper\WPMLChecker;
use ACPT\Integrations\WPML\Helper\WPMLConfig;
use ACPT\Integrations\WPML\Provider\MetaFieldsProvider;

class ACPT_WPML extends AbstractIntegration
{
    /**
     * @inheritDoc
     */
    protected function name()
    {
        return "wpml";
    }

	/**
	 * @inheritDoc
	 */
	protected function isActive()
	{
		$isActive = WPMLChecker::isActive();

		if(!$isActive){
			return false;
		}

		if(ACPT_ENABLE_META != 1){
			$isActive = false;
		}

		if(!$isActive and !PolylangChecker::isActive()){
			WPMLConfig::destroy();
		}

		return $isActive;
	}

	/**
	 * @inheritDoc
	 */
	protected function runIntegration()
	{
	    // do nothing
	}
}
