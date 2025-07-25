<?php

/**
 * Debug menu page controller
 *
 * @package   Duplicator
 * @copyright (c) 2022, Snap Creek LLC
 */

namespace Duplicator\Controllers;

use Duplicator\Core\CapMng;
use Duplicator\Core\Controllers\ControllersManager;
use Duplicator\Core\Controllers\AbstractMenuPageController;

class DebugPageController extends AbstractMenuPageController
{
    /**
     * Class constructor
     */
    protected function __construct()
    {
        $this->parentSlug   = ControllersManager::MAIN_MENU_SLUG;
        $this->pageSlug     = ControllersManager::DEBUG_SUBMENU_SLUG;
        $this->pageTitle    = __('Testing Interface', 'duplicator-pro');
        $this->menuLabel    = __('Debug', 'duplicator-pro');
        $this->capatibility = CapMng::CAP_SETTINGS;
        $this->menuPos      = 40;

        add_action('duplicator_render_page_content_' . $this->pageSlug, array($this, 'renderContent'), 10, 2);
    }

    /**
     * Return true if current page is enabled
     *
     * @return boolean
     */
    public function isEnabled()
    {
        if (!is_admin()) {
            return false;
        }
        $global = \DUP_PRO_Global_Entity::getInstance();
        return $global->debug_on;
    }

    /**
     * Render page content
     *
     * @param string[] $currentLevelSlugs current menu slugs
     * @param string   $innerPage         current inner page, empty if not set
     *
     * @return void
     */
    public function renderContent($currentLevelSlugs, $innerPage)
    {
        require(DUPLICATOR____PATH . '/views/debug/main.php');
    }

    /**
     * Test setup form
     *
     * @param mixed[] $CTRL input values
     *
     * @return void
     */
    public static function testSetup($CTRL)
    {
        $title    = $CTRL['Title'];
        $action   = $CTRL['Action'];
        $testable = $CTRL['Test'] ? 1 : 0;
        $test_css = $testable ? '' : 'style="display:none"';
        $nonce    = isset($CTRL['nonce']) ? $CTRL['nonce'] : wp_create_nonce($action);
        ?>
        <div class="keys">
            <input type="hidden" name="testable" value="<?php echo (int) $testable ?>" />
            <input type="hidden" name="action" value="<?php echo esc_attr($action) ?>" />
            <input type="hidden" name="nonce" value="<?php echo esc_attr($nonce) ?>" />
            <span class="result"><i class="fa fa-cube  fa-lg"></i></span>
            <input type='checkbox' id='<?php echo esc_attr($action) ?>' name='<?php echo esc_attr($action) ?>' <?php echo esc_attr($test_css) ?> /> 
            <label for='<?php echo esc_attr($action) ?>'><?php echo esc_html($title) ?></label> &nbsp;
            <a href="javascript:void(0)" onclick="jQuery(this).closest('form').find('div.params').toggle()">
                <?php esc_html_e('Params', 'duplicator-pro') ?>
            </a> |
            <a href="javascript:void(0)" onclick="jQuery(this).closest('form').submit()">
                <?php esc_html_e('Test', 'duplicator-pro') ?>
            </a>
        </div>
        <?php
    }
}
