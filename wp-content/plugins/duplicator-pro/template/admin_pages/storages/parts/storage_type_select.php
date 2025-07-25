<?php

/**
 * Duplicator messages sections
 *
 * @package   Duplicator
 * @copyright (c) 2022, Snap Creek LLC
 */

use Duplicator\Models\Storages\AbstractStorageEntity;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng  $tplMng
 * @var array<string, mixed> $tplData
 * @var AbstractStorageEntity $storage
 */
$storage = $tplData["storage"];

$sTypeSelected = ($storage->isSelectable() ? $storage->getSType() : -1);
$types         = AbstractStorageEntity::getResisteredTypesByPriority();

if ($storage->getId() < 0) {
    $supportedNotices = [];
    ?>
    <select id="change-mode" name="storage_type" onchange="DupPro.Storage.ChangeMode()">
        <?php foreach ($types as $type) {
            $class = AbstractStorageEntity::getSTypePHPClass($type);
            call_user_func([$class, 'isSelectable']);
            if (!call_user_func([$class, 'isSelectable'])) {
                continue;
            }
            if (!call_user_func([$class, 'isSupported'])) {
                $supportedNotices[] = call_user_func([$class, 'getNotSupportedNotice']);
                continue;
            }
            $name = call_user_func([$class, 'getStypeName']);
            ?>
            <option value="<?php echo (int) $type; ?>" <?php selected($sTypeSelected, $type); ?>>
                <?php echo esc_html($name) ?>
            </option>
        <?php } ?>
    </select>
    <?php
    if (count($supportedNotices) > 0) { ?>
        <div class="margin-top-1" >
        <small class="dpro-store-type-notice"><b><?php esc_html_e('Unsupported storages: ', 'duplicator-pro'); ?></b></small><br>
        <?php foreach ($supportedNotices as $notice) { ?>
            <small class="dpro-store-type-notice">
                <?php
                echo '- ',
                wp_kses(
                    $notice,
                    [
                        'a' => [
                            'href'   => [],
                            'target' => [],
                        ],
                    ]
                );
                ?>
            </small>
            <br>
        <?php } ?>
        </div>
        <?php
    }
} else {
    ?>
    <span id="dup-storage-mode-fixed" data-storage-type="<?php echo (int) $storage->getSType(); ?>">
        <?php echo $storage->getStypeIcon(); ?>&nbsp;<b><?php echo esc_html($storage->getStypeName()); ?></b>
    </span>
    <?php
} ?>

<script>
    jQuery(document).ready(function ($) {
        DupPro.Storage.BindParsley = function (node)
        {
            $('#dup-storage-form').parsley().destroy();
            $('#dup-storage-form .provider input').attr('data-parsley-excluded', 'true');
            $('#dup-storage-form .provider input').prop('disabled', true);

            node.find('input').removeAttr('data-parsley-excluded');
            node.find('input').prop('disabled', false);

            $('#dup-storage-form').parsley();
        };
        
        DupPro.Storage.Autofill = function (mode) {
            switch (parseInt(mode)) {
                case <?php echo (int) \Duplicator\Models\Storages\BackblazeStorage::getSType(); ?>:
                    autoFillRegion(mode, 1);
                    break;
                case <?php echo (int) \Duplicator\Models\Storages\DreamStorage::getSType(); ?>:
                case <?php echo (int) \Duplicator\Models\Storages\VultrStorage::getSType(); ?>:
                case <?php echo (int) \Duplicator\Models\Storages\DigitalOceanStorage::getSType(); ?>:
                    autoFillRegion(mode, 0);
                    break;
                case <?php echo (int) \Duplicator\Models\Storages\WasabiStorage::getSType(); ?>:
                    let wasabiRegion   = $("#s3_region_" + mode);
                    let wasabiEndpoint = $("#s3_endpoint_" + mode);

                    if (wasabiRegion.val().length > 0) {
                        wasabiEndpoint.val("s3." + wasabiRegion.val() + ".wasabisys.com");
                    }

                    wasabiRegion.change(function(e) {
                        let regionVal = $(this).val();
                        if (regionVal.length > 0) {
                            wasabiEndpoint.val("s3." + regionVal + ".wasabisys.com");
                        } else {
                            wasabiEndpoint.val("");
                        }
                    });
                    break;
            }

            function autoFillRegion(type, regionPos) {
                let region      = $("#s3_region_" + type);
                let endpoint    = $("#s3_endpoint_" + type);

                bindEndpointToRegion(region, endpoint, regionPos);

                endpoint.change(function(e) {
                    bindEndpointToRegion(region, endpoint, regionPos);
                });
            }

            function bindEndpointToRegion(region, endpoint, pos) {
                if (endpoint.val().length > 0) {
                    let regionStr = endpoint.val().replace(/.*:\/\//g,'').split(".")[pos];
                    region.val(regionStr);
                } else {
                    region.val("");
                }
            }
        }

        // GENERAL STORAGE LOGIC
        DupPro.Storage.ChangeMode = function (animateOverride) {
            let mode = 0;
            if ($('#dup-storage-mode-fixed').length > 0) {
                mode = $('#dup-storage-mode-fixed').data('storage-type');
            } else {
                let optionSelected = $("#change-mode option:selected");
                mode = optionSelected.val();
            }
            

            let animate = 400;
            let providerConfigNode =  $('#provider-' + mode);
            if (arguments.length == 1) {
                animate = animateOverride;
            }
            $('.provider').hide();
            providerConfigNode.show(animate);
            DupPro.Storage.BindParsley(providerConfigNode);
            DupPro.Storage.Autofill(mode);
        }

        $('#dup-storage-form').parsley();
        DupPro.Storage.ChangeMode(0);
    });
</script>

