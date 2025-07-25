<?php

/**
 * @package Duplicator
 */

use Duplicator\Core\CapMng;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 * @var array<string, mixed> $tplData
 * @var ?DUP_PRO_Package $package
 */
$package = $tplData['package'];

/** @var int */
$status = $tplData['status'];

if ($status <= DUP_PRO_PackageStatus::PRE_PROCESS || $status >= DUP_PRO_PackageStatus::COMPLETE) {
    return;
}

$progress_html    = "<span style='display:none' id='status-{$package->ID}'>{$status}</span>";
$stop_button_text = __('Stop', 'duplicator-pro');


if ($status >= 75) {
    $stop_button_text = __('Stop Transfer', 'duplicator-pro');
    $progress_html    = "<i class='fa fa-sync fa-sm fa-spin'></i>&nbsp;<span id='status-progress-{$package->ID}'>0</span>%"
    . "<span style='display:none' id='status-{$package->ID}'>{$status}</span>";
} elseif ($status > 0) {
    $stop_button_text = __('Stop Build', 'duplicator-pro');
    $progress_html    = "<i class='fa fa-cog fa-sm fa-spin'></i>&nbsp;<span id='status-{$package->ID}'>{$status}</span>%";
} else {
    // In a pending state
    $stop_button_text = __('Cancel Pending', 'duplicator-pro');
    $progress_html    = "<span style='display:none' id='status-{$package->ID}'>{$status}</span>";
}

?>
<tr class="dup-row-progress">
    <td colspan="11">
        <div class="wp-filter dup-build-msg">
            <?php if ($status < DUP_PRO_PackageStatus::STORAGE_PROCESSING) : ?>
                <!-- BUILDING PROGRESS-->
                <div id='dpro-progress-status-message-build'>
                    <div class='status-hdr'>
                        <?php _e('Building Package', 'duplicator-pro'); ?>&nbsp;<?php echo $progress_html; ?>
                    </div>
                    <small>
                        <?php _e('Please allow it to finish before creating another one.', 'duplicator-pro'); ?>
                    </small>
                </div>
            <?php else : ?>
                <!-- TRANSFER PROGRESS -->
                <div id='dpro-progress-status-message-transfer'>
                    <div class='status-hdr'>
                        <?php _e('Transferring Package', 'duplicator-pro'); ?>&nbsp;<?php echo $progress_html; ?>
                    </div>
                    <small id="dpro-progress-status-message-transfer-msg">
                        <?php _e('Getting Transfer State...', 'duplicator-pro'); ?>
                    </small>
                </div>
            <?php endif; ?>
            <div id="dup-progress-bar-area">
                <div class="dup-pro-meter-wrapper">
                    <div class="dup-pro-meter blue dup-pro-fullsize">
                        <span></span>
                    </div>
                    <span class="text"></span>
                </div>
            </div>
            <?php if (CapMng::can(CapMng::CAP_CREATE, false)) { ?>
            <button onclick="DupPro.Pack.StopBuild(<?php echo $package->ID; ?>); return false;" class="button button-large dup-build-stop-btn">
                <i class="fa fa-times fa-sm"></i> &nbsp; <?php echo $stop_button_text; ?>
            </button>
            <?php } ?>
        </div>
    </td>
</tr>
