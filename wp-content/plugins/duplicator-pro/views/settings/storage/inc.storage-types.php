<?php
defined("ABSPATH") or die("");

use Duplicator\Core\Controllers\ControllersManager;
use Duplicator\Models\Storages\StoragesUtil;

$global = DUP_PRO_Global_Entity::getInstance();
?>
<form id="dup-settings-form" action="<?php echo ControllersManager::getCurrentLink(); ?>" method="post" data-parsley-validate>
    <?php require('hidden.fields.widget.php'); ?>
    <?php StoragesUtil::renderGlobalOptions(); ?>
    <p class="submit dpro-save-submit">
        <input 
            type="submit" 
            name="submit" 
            id="submit" 
            class="button-primary" 
            value="<?php esc_attr_e('Save Storage Settings', 'duplicator-pro') ?>" 
            style="display: inline-block;" 
        >
    </p>
</form>