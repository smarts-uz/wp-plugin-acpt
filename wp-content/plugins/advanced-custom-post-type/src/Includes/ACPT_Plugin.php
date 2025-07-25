<?php

namespace ACPT\Includes;

use ACPT\Admin\ACPT_Admin;
use ACPT\Core\Models\Settings\SettingsModel;
use ACPT\Core\Repository\SettingsRepository;
use ACPT\Utils\Settings\Settings;
use Omnipay\Common\Exception\RuntimeExceptionTest;
use Phpfastcache\Core\Pool\ExtendedCacheItemPoolInterface;

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    advanced-custom-post-type
 * @subpackage advanced-custom-post-type/includes
 * @author     Mauro Cassani <maurocassani1978@gmail.com>
 */
class ACPT_Plugin
{
    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      ACPT_Loader $loader Maintains and registers all hooks for the plugin.
     */
    private $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $name The string used to uniquely identify this plugin.
     */
    private $name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    private $version;

    /**
     * Static cache instance.
     *
     * @var ExtendedCacheItemPoolInterface
     */
    private static $cache;

    /**
     * The code that runs during plugin activation.
     * @throws \Exception
     */
    public function activationHook()
    {
        ACPT_Activator::activate();
    }

    /**
     * The code that runs during plugin deactivation.
     * @throws \Exception
     */
    public function deactivationHook()
    {
        ACPT_Deactivator::deactivate();
    }

    /**
     * ACPT_Plugin constructor.
     */
    public function __construct()
    {
        $this->loader = new ACPT_Loader();
    }

    /**
     * Entry point of the application
     *
     * @throws \Exception
     * @since    1.0.0
     */
    public function run()
    {
        $this->setCache();
        $this->initACPTDB();
        $this->initSettingsConstants();
        $this->syncDBAndRunHealthCheck();
        $this->disableACPTLite();
        $this->setName();
        $this->setVersion();
        $this->runInternalization();
        $this->runAdmin();
        $this->loader->run();
    }

    /**
     * Set plugin cache instance
     */
    public static function setCache()
    {
        try {
            $settings = SettingsRepository::get();
            $cacheEnabled = Settings::fromSettings(SettingsModel::ENABLE_CACHE, $settings) ?? true;

            if($cacheEnabled){
                $cacheDriver = Settings::fromSettings(SettingsModel::CACHE_DRIVER, $settings) ?? "files";
                $cacheConfig = Settings::fromSettings(SettingsModel::CACHE_CONFIG, $settings) ?? ["folder" => "cache"];
                $cache = new ACPT_Cache($cacheDriver, (array)$cacheConfig);

                self::$cache = $cache->getInstance();
            }
        } catch (\Exception $exception){
        }
    }

    /**
     * Get plugin cache instance
     *
     * @return ExtendedCacheItemPoolInterface|null
     */
    public static function getCache()
    {
        return self::$cache;
    }

    /**
     * @return bool
     */
    public static function isCacheEnabled()
    {
        return self::$cache instanceof ExtendedCacheItemPoolInterface;
    }

    /**
     * Init the DB
     */
    private function initACPTDB()
    {
        try {
            $cache = self::getCache();

            if($cache !== null){
                ACPT_DB::injectCache($cache);
            }
        } catch (\Exception $exception){
            // do nothing
        }
    }

    /**
     * Define settings constants
     */
    private function initSettingsConstants()
    {
        define('ACPT_SKIN', Settings::get(SettingsModel::SKIN) ?? 'light');
        define('ACPT_ENABLE_META_CACHE', Settings::get(SettingsModel::ENABLE_META_CACHE) ?? false);
        define('ACPT_ENABLE_META', Settings::get(SettingsModel::ENABLE_META) ?? true);
        define('ACPT_ENABLE_CPT', Settings::get(SettingsModel::ENABLE_CPT) ?? true);
        define('ACPT_ENABLE_TAX', Settings::get(SettingsModel::ENABLE_TAX) ?? true);
        define('ACPT_ENABLE_PAGES', Settings::get(SettingsModel::ENABLE_OP) ?? true);
        define('ACPT_ENABLE_BLOCKS', Settings::get(SettingsModel::ENABLE_BLOCKS) ?? false);
        define('ACPT_ENABLE_FORMS', Settings::get(SettingsModel::ENABLE_FORMS) ?? false);
        define('ACPT_ENABLE_BETA', Settings::get(SettingsModel::ENABLE_BETA) ?? false);
        define('ACPT_DELETE_TABLES_WHEN_DEACTIVATE_KEY', Settings::get(SettingsModel::DELETE_TABLES_WHEN_DEACTIVATE_KEY) ?? false);
        define('ACPT_DELETE_POSTS_KEY', Settings::get(SettingsModel::DELETE_POSTS_KEY) ?? false);
        define('ACPT_DELETE_POSTMETA_KEY', Settings::get(SettingsModel::DELETE_POSTMETA_KEY) ?? false);
        define('ACPT_DELETE_UNUSED_TABLES', Settings::get(SettingsModel::DELETE_UNUSED_TABLES) ?? false);
    }

    /**
     * @throws \Exception
     */
    private function syncDBAndRunHealthCheck()
    {
        $cacheKey = md5("AbstractRepository_syncDBAndRunHealthCheck");
        $cacheTtl = 3600;
        $checkIfSchemaExists = self::isCacheEnabled() ? self::$cache->getItem($cacheKey) : ACPT_DB::checkIfSchemaExists();

        if(!$checkIfSchemaExists){
            $checkIfSchemaExists = ACPT_DB::checkIfSchemaExists();

            if(self::isCacheEnabled()){
                $cachedElement = self::$cache->getItem($cacheKey);
                $tag = md5(static::class);
                $cachedElement->addTag($tag)->set(1)->expiresAfter($cacheTtl);
                self::$cache->save($cachedElement);
            }
        }

        if(false === $checkIfSchemaExists){
            $old_version = get_option('acpt_version', 0);
            ACPT_DB::createSchema(ACPT_PLUGIN_VERSION, get_option('acpt_current_version') ?? oldACPTPluginVersion($old_version));
            ACPT_DB::sync();
        }

        if(false === ACPT_DB::checkIfNativePostsExists()){
            ACPT_DB::sync();
        }

        ACPT_DB_Tools::runHealthCheck();
    }

    /**
     * Disable ACPT Lite plugin if it's yet active
     */
    private function disableACPTLite()
    {
        $pluginsToDeactivate = [];

        // versions prior to 2.0.6
        $pluginLite = 'advanced-custom-post-type-lite/advanced-custom-post-type-lite.php';

        // plugin root file was changed in ACPT Lite v2.0.6
        $pluginLiteAfter206 = 'acpt-lite/acpt-lite.php';

        if (is_plugin_active($pluginLite) ) {
            $pluginsToDeactivate[] = $pluginLite;
        } elseif(is_plugin_active($pluginLiteAfter206)){
            $pluginsToDeactivate[] = $pluginLiteAfter206;
        }

        if(!empty($pluginsToDeactivate)){
            ACPT_Lite_Importer::import();
            deactivate_plugins($pluginsToDeactivate);
        }
    }

    /**
     * Set plugin name
     */
    private function setName()
    {
        if ( defined( 'ACPT_PLUGIN_NAME' ) ) {
            $this->name = ACPT_PLUGIN_NAME;
        } else {
            $this->name = plugin_dir_path( __FILE__ );
        }
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set plugin version
     */
    private function setVersion()
    {
        if ( defined( 'ACPT_PLUGIN_VERSION' ) ) {
            $this->version = ACPT_PLUGIN_VERSION;
        } else {
            $this->version = '1.0.0';
        }
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the advanced-custom-post-typeInternalization class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function runInternalization()
    {
        $i18n = new ACPT_Internalization();
        $i18n->run();
    }

    /**
     * Run all scripts related to the admin area functionality
     * of the plugin.
     *
     * @throws \Exception
     * @since    1.0.0
     * @access   private
     */
    private function runAdmin()
    {
        $admin = new ACPT_Admin($this->loader);
        $admin->run();
    }
}