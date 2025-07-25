<?php

/**
 * Auloader calsses
 *
 * @package   Duplicator
 * @copyright (c) 2022, Snap Creek LLC
 */

 namespace Duplicator\Addons\FtpAddon\Utils;

use Duplicator\Addons\FtpAddon\FtpAddon;
use Duplicator\Utils\AbstractAutoloader;

/**
 * Autoloader calss, dont user Duplicator library here
 */
final class Autoloader extends AbstractAutoloader
{
    const VENDOR_PATH = FtpAddon::ADDON_PATH . '/vendor-prefixed/';

    /**
     * Register autoloader function
     *
     * @return void
     */
    public static function register()
    {
        spl_autoload_register([__CLASS__, 'load']);
    }

    /**
     * Load class
     *
     * @param string $className class name
     *
     * @return void
     */
    public static function load($className)
    {
        foreach (self::getNamespacesVendorMapping() as $namespace => $mappedPath) {
            if (strpos($className, $namespace) !== 0) {
                continue;
            }

            $filepath = self::getFilenameFromClass($className, $namespace, $mappedPath);
            if (file_exists($filepath)) {
                include $filepath;
                return;
            }
        }
    }

    /**
     * Return namespace mapping
     *
     * @return string[]
     */
    protected static function getNamespacesVendorMapping()
    {
        return [
            self::ROOT_VENDOR . 'phpseclib' => self::VENDOR_PATH . 'phpseclib/phpseclib/phpseclib/',
        ];
    }
}
