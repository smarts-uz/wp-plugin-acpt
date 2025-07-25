<?php

namespace Duplicator\Addons\GDriveAddon\Utils;

use Duplicator\Addons\GDriveAddon\GDriveAddon;
use Duplicator\Utils\AbstractAutoloader;

class Autoloader extends AbstractAutoloader
{
    /**
     * Register autoloader function
     *
     * @return void
     */
    public static function register()
    {
        /**
         * Legacy classes
         */
        //require_once GDriveAddon::ADDON_PATH . '/lib/google/apiclient/autoload.php';
        require_once GDriveAddon::ADDON_PATH . '/vendor-prefixed/google/apiclient/src/Google/autoload.php';
        require_once GDriveAddon::ADDON_PATH . '/classes/class.u.gdrive.php';
        require_once GDriveAddon::ADDON_PATH . '/classes/class.enhanced.google.media.file.upload.php';
    }
}
