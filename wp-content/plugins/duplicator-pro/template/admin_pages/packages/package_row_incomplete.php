<?php

/**
 * @package Duplicator
 */

use Duplicator\Controllers\PackagesPageController;
use Duplicator\Package\Recovery\RecoveryPackage;
use Duplicator\Views\UserUIOptions;

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
$global  = DUP_PRO_Global_Entity::getInstance();

/** @var int */
$status = $tplData['status'];

if ($status >= DUP_PRO_PackageStatus::COMPLETE) {
    return;
}

global $packagesViewData;


$isRecoveable      = RecoveryPackage::isPackageIdRecoveable($package->ID);
$isRecoverPoint    = (RecoveryPackage::getRecoverPackageId() === $package->ID);
$pack_name         = $package->Name;
$pack_archive_size = $package->Archive->Size;
$pack_namehash     = $package->NameHash;
$pack_dbonly       = $package->isDBOnly();
$brand             = $package->Brand;

//Links
$uniqueid         = $package->NameHash;
$archive_exists   = ($package->getLocalPackageFilePath(DUP_PRO_Package_File_Type::Archive) != false);
$installer_exists = ($package->getLocalPackageFilePath(DUP_PRO_Package_File_Type::Installer) != false);
$progress_error   = '';

//ROW CSS
$rowClasses   = array('');
$rowClasses[] = 'dup-row';
$rowClasses[] = 'dup-row-incomplete';
$rowClasses[] = ($packagesViewData['rowCount'] % 2 == 0) ? 'dup-row-alt-dark' : 'dup-row-alt-light';
$rowClasses[] = ($isRecoverPoint) ? 'dup-recovery-package' : '';
$rowCSS       = trim(implode(' ', $rowClasses));


//ArchiveInfo
$archive_name         = $package->Archive->File;
$archiveDownloadURL   = $package->getLocalPackageFileURL(DUP_PRO_Package_File_Type::Archive);
$installerDownloadURL = $package->getLocalPackageFileURL(DUP_PRO_Package_File_Type::Installer);
$installerFullName    = $package->Installer->getInstallerName();

$createdFormat = UserUIOptions::getInstance()->get(UserUIOptions::VAL_CREATED_DATE_FORMAT);

//Lang Values
$txt_DatabaseOnly = __('Database Only', 'duplicator-pro');

$cellErrCSS = '';

if ($status < DUP_PRO_PackageStatus::COPIEDPACKAGE) {
    // In the process of building
    $size      = 0;
    $tmpSearch = glob(DUPLICATOR_PRO_SSDIR_PATH_TMP . "/{$pack_namehash}_*");

    if (is_array($tmpSearch)) {
        $result = @array_map('filesize', $tmpSearch);
        $size   = array_sum($result);
    }
    $pack_archive_size = $size;
}

$packageDetailsURL = PackagesPageController::getInstance()->getPackageDetailsURL($package->ID);

$progress_html    = "<span style='display:none' id='status-{$package->ID}'>{$status}</span>";
$stop_button_text = __('Stop', 'duplicator-pro');

if ($status >= 0) {
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
} else {
    //FAILURES AND CANCELLATIONS
    switch ($status) {
        case DUP_PRO_PackageStatus::ERROR:
            $cellErrCSS     = 'dup-cell-err';
            $progress_error = '<div class="progress-error">'
            . '<a type="button" class="dup-cell-err-btn button" href="' . esc_url($packageDetailsURL) . '">'
            . '<i class="fa fa-exclamation-triangle fa-xs"></i>&nbsp;'
            .  __('Error Processing', 'duplicator-pro') . "</a></div><span style='display:none' id='status-" . $package->ID . "'>$status</span>";
            break;
        case DUP_PRO_PackageStatus::BUILD_CANCELLED:
            $cellErrCSS     = 'dup-cell-cancelled';
            $progress_error = '<div class="progress-error"><i class="fas fa-info-circle  fa-sm"></i>&nbsp;'
            . __('Build Cancelled', 'duplicator-pro') . "</div><span style='display:none' id='status-" . $package->ID . "'>$status</span>";
            break;
        case DUP_PRO_PackageStatus::PENDING_CANCEL:
            $progress_error = '<div class="progress-error"><i class="fas fa-info-circle  fa-sm"></i> '
            . __('Cancelling Build', 'duplicator-pro') . "</div><span style='display:none' id='status-"
            . $package->ID . "'>$status</span>";
            break;
        case DUP_PRO_PackageStatus::STORAGE_CANCELLED:
            $cellErrCSS     = 'dup-cell-cancelled';
            $progress_error = '<div class="progress-error"><i class="fas fa-info-circle  fa-sm"></i>&nbsp;'
            . __('Storage Cancelled', 'duplicator-pro') . "</div><span style='display:none' id='status-" . $package->ID . "'>$status</span>";
            break;
        case DUP_PRO_PackageStatus::REQUIREMENTS_FAILED:
            $package_id            = $package->ID;
            $package               = DUP_PRO_Package::get_by_id($package_id);
            $package_log_store_dir = trailingslashit(dirname($package->StorePath));
            $is_txt_log_file_exist = file_exists("{$package_log_store_dir}{$package->NameHash}_log.txt");
            if ($is_txt_log_file_exist) {
                $link_log = "{$package->StoreURL}{$package->NameHash}_log.txt";
            } else {
                // .log is for backward compatibility
                $link_log = "{$package->StoreURL}{$package->NameHash}.log";
            }
            $progress_error = '<div class="progress-error"><a href="' . esc_url($link_log) . '" target="_blank">'
            . '<i class="fas fa-info-circle"></i> '
            . __('Requirements Failed', 'duplicator-pro') . "</a></div>"
            . "<span style='display:none' id='status-" . $package->ID . "'>$status</span>";
            break;
    }
}
?>

<tr id="dup-row-pack-id-<?php echo $package->ID; ?>" data-package-id="<?php echo $package->ID; ?>" class="<?php echo $rowCSS; ?>" >
    <td class="dup-check-column dup-cell-chk">
        <label for="<?php echo $package->ID; ?>">
        <input name="delete_confirm"
                type="checkbox" id="<?php echo $package->ID;?>"
                <?php echo ($status >= DUP_PRO_PackageStatus::PRE_PROCESS) ? 'disabled="disabled"' : ''; ?> />
        </label>
    </td>
    <td class="dup-name-column dup-cell-name">
        <?php echo esc_html($pack_name); ?>
    </td>
    <td class="dup-note-column">
    </td>
    <td class="dup-storages-column">
    </td>
    <td class="dup-flags-column">
        <?php $tplMng->render('admin_pages/packages/row_parts/falgs_cell'); ?>
    </td>
    <td class="dup-created-column" >
        <?php echo DUP_PRO_Package::format_and_get_local_date_time($package->getCreated(), $createdFormat); ?>
    </td>
    <td class="dup-age-column">
        <?php echo esc_html($package->getPackageLife('human')); ?>
    </td>
    <td class="dup-size-column" >
        <?php if ($status >= DUP_PRO_PackageStatus::PRE_PROCESS) {
            $package->get_display_size();
        } else {
            esc_html_e('N/A', 'duplicator-pro');
        }?>
    </td>
    <td class="dup-cell-incomplete <?php echo $cellErrCSS; ?> no-select" colspan="3">
        <?php if ($status >= DUP_PRO_PackageStatus::PRE_PROCESS) { ?>
            <i><?php esc_html_e('Building Package Files...', 'duplicator-pro'); ?></i>
        <?php } else {
            echo $progress_error;
        }?>
    </td>
</tr>

