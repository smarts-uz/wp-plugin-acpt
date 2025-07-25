<?php

/**
 *
 * @package   Duplicator
 * @copyright (c) 2022, Snap Creek LLC
 */

namespace Duplicator\Models\Storages;

use DUP_PRO_Global_Entity;
use DUP_PRO_Log;
use DUP_PRO_OneDrive_Config;
use DUP_PRO_Onedrive_U;
use DUP_PRO_Package;
use DUP_PRO_Package_File_Type;
use DUP_PRO_Package_Upload_Info;
use DUP_PRO_STR;
use DUP_PRO_U;
use Duplicator\Core\Views\TplMng;
use Duplicator\Libs\Snap\SnapLog;
use Duplicator\Libs\Snap\SnapUtil;
use Duplicator\Models\DynamicGlobalEntity;
use Duplicator\Utils\OAuth\TokenEntity;
use Duplicator\Utils\OAuth\TokenService;
use DuplicatorPro\Krizalys\Onedrive\Client as OneDriveClient;
use DuplicatorPro\Krizalys\Onedrive\Folder as OneDriveFolder;
use DuplicatorPro\Krizalys\Onedrive\ResumableUploader;
use Exception;

class OneDriveStorage extends AbstractStorageEntity implements StorageAuthInterface
{
    /** @var ?OneDriveClient */
    protected $client = null;

    /**
     * Get default config
     *
     * @return array<string,scalar>
     */
    protected static function getDefaultConfig()
    {
        $config = parent::getDefaultConfig();
        $config = array_merge(
            $config,
            [
                'endpoint_url'           => '',
                'resource_id'            => '',
                'access_token'           => '',
                'refresh_token'          => '',
                'token_obtained'         => 0,
                'user_id'                => '',
                'storage_folder_id'      => '',
                'storage_folder_web_url' => '',
                'all_folders_perm'       => false,
                'authorized'             => false,
            ]
        );


        return $config;
    }

    /**
     * Serialize
     *
     * Wakeup method.
     *
     * @return void
     */
    public function __wakeup()
    {
        parent::__wakeup();

        if ($this->legacyEntity) {
            // Old storage entity
            $this->legacyEntity = false;
            // Make sure the storage type is right from the old entity
            $this->storage_type = $this->getSType();
            $this->config       = [
                'endpoint_url'           => $this->onedrive_endpoint_url,
                'resource_id'            => $this->onedrive_resource_id,
                'access_token'           => $this->onedrive_access_token,
                'refresh_token'          => $this->onedrive_refresh_token,
                'token_obtained'         => $this->onedrive_token_obtained,
                'user_id'                => $this->onedrive_user_id,
                'storage_folder'         => ltrim($this->onedrive_storage_folder, '/\\'),
                'max_packages'           => $this->onedrive_max_files,
                'storage_folder_id'      => $this->onedrive_storage_folder_id,
                'storage_folder_web_url' => $this->onedrive_storage_folder_web_url,
                'authorized'             => ($this->onedrive_authorization_state == 1),
            ];
            // reset old values
            $this->onedrive_endpoint_url           = '';
            $this->onedrive_resource_id            = '';
            $this->onedrive_access_token           = '';
            $this->onedrive_refresh_token          = '';
            $this->onedrive_token_obtained         = 0;
            $this->onedrive_user_id                = '';
            $this->onedrive_storage_folder         = '';
            $this->onedrive_max_files              = 10;
            $this->onedrive_storage_folder_id      = '';
            $this->onedrive_authorization_state    = 0;
            $this->onedrive_storage_folder_web_url = '';
        }
    }

    /**
     * Will be called, automatically, when Serialize
     *
     * @return array<string, mixed>
     */
    public function __serialize() // phpcs:ignore PHPCompatibility.FunctionNameRestrictions.NewMagicMethods.__serializeFound
    {
        $data = parent::__serialize();
        unset($data['client']);
        return $data;
    }

    /**
     * Return the storage type
     *
     * @return int
     */
    public static function getSType()
    {
        return 7;
    }

    /**
     * Returns the storage type icon.
     *
     * @return string Returns the storage icon
     */
    public static function getStypeIcon()
    {
        $imgUrl = DUPLICATOR_PRO_IMG_URL . '/onedrive.svg';
        return '<img src="' . esc_url($imgUrl) . '" class="dup-storage-icon" alt="' . esc_attr(static::getStypeName()) . '" />';
    }

    /**
     * Returns the storage type name.
     *
     * @return string
     */
    public static function getStypeName()
    {
        return __('OneDrive', 'duplicator-pro');
    }

    /**
     * Get storage location string
     *
     * @return string
     */
    public function getLocationString()
    {
        if (!$this->isAuthorized()) {
            return __("Not Authenticated", "duplicator-pro");
        } else {
            return $this->config['storage_folder_web_url'];
        }
    }

    /**
     * Returns an html anchor tag of location
     *
     * @return string Returns an html anchor tag with the storage location as a hyperlink.
     *
     * @example
     * OneDrive Example return
     * <a target="_blank" href="https://1drv.ms/f/sAFrQtasdrewasyghg">https://1drv.ms/f/sAFrQtasdrewasyghg</a>
     */
    public function getHtmlLocationLink()
    {
        if ($this->isAuthorized()) {
            return '<a href="' . esc_url($this->getLocationString()) . '" target="_blank" >' . esc_html($this->getLocationString()) . '</a>';
        } else {
            return $this->getLocationString();
        }
    }

    /**
     * Check if storage is supported
     *
     * @return bool
     */
    public static function isSupported()
    {
        return SnapUtil::isCurlEnabled();
    }

    /**
     * Get supported notice, displayed if storage isn't supported
     *
     * @return string html string or empty if storage is supported
     */
    public static function getNotSupportedNotice()
    {
        if (static::isSupported()) {
            return '';
        }

        return esc_html__('OneDrive requires the PHP CURL extension enabled.', 'duplicator-pro');
    }

    /**
     * Check if storage is valid
     *
     * @return bool Return true if storage is valid and ready to use, false otherwise
     */
    public function isValid()
    {
        return $this->isAuthorized();
    }

    /**
     * Is autorized
     *
     * @return bool
     */
    public function isAuthorized()
    {
        return $this->config['authorized'];
    }

    /**
     * Authorized from HTTP request
     *
     * @param string $message Message
     *
     * @return bool True if authorized, false if failed
     */
    public function authorizeFromRequest(&$message = '')
    {
        try {
            if (($refreshToken = SnapUtil::sanitizeTextInput(SnapUtil::INPUT_REQUEST, 'auth_code')) === '') {
                throw new Exception(__('Authorization code is empty', 'duplicator-pro'));
            }

            $this->name                     = SnapUtil::sanitizeTextInput(SnapUtil::INPUT_REQUEST, 'name', '');
            $this->notes                    = SnapUtil::sanitizeDefaultInput(SnapUtil::INPUT_REQUEST, 'notes', '');
            $this->config['max_packages']   = SnapUtil::sanitizeIntInput(SnapUtil::INPUT_REQUEST, 'max_packages', 10);
            $this->config['storage_folder'] = self::getSanitizedInputFolder('storage_folder', 'remove');

            $this->revokeAuthorization();

            $token = (new TokenEntity(self::getSType(), ['refresh_token' => $refreshToken]));
            $token->refresh();

            $this->config['access_token']   = $token->getAccessToken();
            $this->config['refresh_token']  = $token->getRefreshToken();
            $this->config['token_obtained'] = $token->getCreated();
            $this->config['endpoint_url']   = $this->config['resource_id'] = '';
            $this->config['authorized']     = true;

            $authClient = $this->getClient();

            DUP_PRO_Log::traceObject("OneDrive Client State:", $authClient->getState());
            $error_message = DUP_PRO_Onedrive_U::getErrorMessageBasedOnClientState($authClient->getState());
            if ($error_message !== null) {
                throw new Exception($error_message);
            }

            $onedrive_info                = $authClient->getServiceInfo();
            $this->config['endpoint_url'] = $onedrive_info['endpoint_url'];
            $this->config['resource_id']  = $onedrive_info['resource_id'];

            $clientState   = $authClient->getState();
            $error_message = DUP_PRO_Onedrive_U::getErrorMessageBasedOnClientState($clientState);
            if ($error_message !== null) {
                throw new Exception($error_message);
            }

            $this->config['user_id'] = property_exists($clientState->token->data, "user_id")
                ? $clientState->token->data->user_id
                : '';

            // Get the storage folder id is done after the authorization
            $this->config['storage_folder_web_url'] = $this->getOneDriveStorageFolder()->getWebURL();
        } catch (Exception $e) {
            DUP_PRO_Log::trace("Problem authorizing Dropbox access token msg: " . $e->getMessage());
            $message = $e->getMessage();
            return false;
        }

        $message = __('OneDrive is connected successfully and Storage Provider Updated.', 'duplicator-pro');
        return true;
    }

    /**
     * Revokes authorization
     *
     * @param string $message Message
     *
     * @return bool True if authorized, false if failed
     */
    public function revokeAuthorization(&$message = '')
    {
        if (!$this->isAuthorized()) {
            $message = __('Onedrive isn\'t authorized.', 'duplicator-pro');
            return true;
        }

        $this->config['endpoint_url']           = '';
        $this->config['resource_id']            = '';
        $this->config['access_token']           = '';
        $this->config['refresh_token']          = '';
        $this->config['token_obtained']         = 0;
        $this->config['user_id']                = '';
        $this->config['storage_folder_id']      = '';
        $this->config['storage_folder_web_url'] = '';
        $this->config['authorized']             = false;
        $this->client                           = null;

        $message = __('Onedrive is disconnected successfully.', 'duplicator-pro');
        return true;
    }

    /**
     * Get external revoke url
     *
     * @return string
     */
    public function getExternalRevokeUrl()
    {
        return DUP_PRO_Onedrive_U::get_onedrive_logout_url(true);
    }

    /**
     * Get authorization URL
     *
     * @return string
     */
    public function getAuthorizationUrl()
    {
        return (new TokenService(static::getSType()))->getRedirectUri();
    }

    /**
     * Get action key text
     *
     * @param string $key Key name (action, pending, failed, cancelled, success)
     *
     * @return string
     */
    protected function getActionKeyText($key)
    {
        switch ($key) {
            case 'action':
                return sprintf(
                    __('Transferring to %1$s folder:<br/> <i>%2$s</i>', "duplicator-pro"),
                    $this->getStypeName(),
                    $this->getStorageFolder()
                );
            case 'pending':
                return sprintf(
                    __('Transfer to %1$s folder %2$s is pending', "duplicator-pro"),
                    $this->getStypeName(),
                    $this->getStorageFolder()
                );
            case 'failed':
                return sprintf(
                    __('Failed to transfer to %1$s folder %2$s', "duplicator-pro"),
                    $this->getStypeName(),
                    $this->getStorageFolder()
                );
            case 'cancelled':
                return sprintf(
                    __('Cancelled before could transfer to %1$s folder %2$s', "duplicator-pro"),
                    $this->getStypeName(),
                    $this->getStorageFolder()
                );
            case 'success':
                return sprintf(
                    __('Transferred package to %1$s folder %2$s', "duplicator-pro"),
                    $this->getStypeName(),
                    $this->getStorageFolder()
                );
            default:
                throw new Exception('Invalid key');
        }
    }

    /**
     * Update all permissions
     *
     * @param bool $allFoldersPerm All folders permission
     *
     * @return bool True if success, false otherwise
     */
    public function setAllPermissions($allFoldersPerm)
    {
        $this->config['all_folders_perm'] = $allFoldersPerm;
        return $this->save();
    }

    /**
     * Render form config fields
     *
     * @param bool $echo Echo or return
     *
     * @return string
     */
    public function renderConfigFields($echo = true)
    {
        return TplMng::getInstance()->render(
            'admin_pages/storages/configs/onedrive',
            [
                'storage'           => $this,
                'storageFolder'     => $this->config['storage_folder'],
                'maxPackages'       => $this->config['max_packages'],
                'allFolderPers'     => $this->config['all_folders_perm'],
                'accountInfo'       => $this->getAccountInfo(),
                'stateToken'        => $this->getStateToken(),
                'externalRevokeUrl' => $this->getExternalRevokeUrl(),
            ],
            $echo
        );
    }

    /**
     * Update data from http request, this method don't save data, just update object properties
     *
     * @param string $message Message
     *
     * @return bool True if success and all data is valid, false otherwise
     */
    public function updateFromHttpRequest(&$message = '')
    {
        if ((parent::updateFromHttpRequest($message) === false)) {
            return false;
        }

        $this->config['max_packages']   = SnapUtil::sanitizeIntInput(SnapUtil::INPUT_REQUEST, 'onedrive_msgraph_max_files', 10);
        $oldFolder                      = $this->config['storage_folder'];
        $this->config['storage_folder'] = self::getSanitizedInputFolder('_onedrive_msgraph_storage_folder', 'remove');

        if ($this->isAuthorized() && $oldFolder != $this->config['storage_folder']) {
            // Create new folder
            $this->config['storage_folder_id']      = '';
            $this->config['storage_folder_web_url'] = '';
            $this->config['storage_folder_web_url'] = $this->getOneDriveStorageFolder()->getWebURL();
        }

        $message = sprintf(
            __('OneDrive Storage Updated.', 'duplicator-pro'),
            $this->config['server'],
            $this->getStorageFolder()
        );
        return true;
    }

    /**
     * Storages test
     *
     * @param string $message Test message
     *
     * @return bool return true if success, false otherwise
     */
    public function test(&$message = '')
    {
        if (parent::test($message) == false) {
            return false;
        }

        $result          = false;
        $source_handle   = null;
        $source_filepath = '';
        try {
            $source_filepath = tempnam(sys_get_temp_dir(), 'DUP');
            if ($source_filepath === false) {
                throw new Exception(__("Couldn't create the temp file for the OneDrive send test", 'duplicator-pro'));
            }
            DUP_PRO_Log::trace("Created temp file $source_filepath");

            $file_name = basename($source_filepath);
            /** @todo add chck of all IO functions return */
            $source_handle = fopen($source_filepath, 'rw+b');
            $rnd           = rand();
            fwrite($source_handle, "$rnd");
            DUP_PRO_Log::trace("Wrote $rnd to $source_filepath");
            fclose($source_handle);
            $source_handle = null;
            $this->testLog->addMessage('Get OneDrive Folder');
            $parent = $this->getOneDriveStorageFolder();
            $this->testLog->addMessage('OneDrive Folder Id: ' . $parent->getId());

            //$test_file = $parent->createFile($file_name,$source_handle);
            //Replacing the createFile method with uploadChunk so
            //we can directly check, if the method we are going to
            //use is working on this set-up.
            $client      = $this->getClient();
            $remote_path = $this->getStorageFolder() . '/' . $file_name;
            $this->testLog->addMessage('Upload test file');
            $this->testLog->addMessage("\tLocal path: " . $source_filepath);
            $this->testLog->addMessage("\tRemote path: " . $remote_path);
            $client->uploadFileChunk($source_filepath, $remote_path);
            $this->testLog->addMessage('Get remote uploaded file');
            $test_file = $client->RUploader->getFile();

            /*
              error_log('-------------------------');
              error_log(print_r($test_file, true));
              error_log('++++++++++++++++++++++++++');
             */
            try {
                if ($test_file->sha1CheckSum($source_filepath)) {
                    $result  = true;
                    $message = esc_html__('Successfully stored and retrieved file', 'duplicator-pro');
                    $client->deleteDriveItem($test_file->getId());
                } else {
                    $message = esc_html__('There was a problem storing or retrieving the temporary file on this account.', 'duplicator-pro');
                }
            } catch (Exception $exception) {
                if ($exception->getCode() == 404 && $client->isBusiness()) {
                    $result  = true;
                    $message = esc_html__('Successfully stored and retrieved file', 'duplicator-pro');
                    $client->deleteDriveItem($test_file->getId());
                } else {
                    $message = sprintf(esc_html__('An error happened. Error message: %s', 'duplicator-pro'), $exception->getMessage());
                }
            }
        } catch (Exception $ex) {
            DUP_PRO_Log::trace(SnapLog::getTextException($ex, true));
            $message = $ex->getMessage();
        }

        if (file_exists($source_filepath)) {
            DUP_PRO_Log::trace("attempting to delete {$source_filepath}");
            unlink($source_filepath);
        }

        if ($result) {
            $this->testLog->addMessage(__('Successfully stored and deleted file', 'duplicator-pro'));
            $message = __('Successfully stored and deleted file', 'duplicator-pro');
            return true;
        } else {
            return false;
        }
    }

    /**
     * Copies the package files from the default local storage to another local storage location
     *
     * @param DUP_PRO_Package             $package     the package
     * @param DUP_PRO_Package_Upload_Info $upload_info the upload info
     *
     * @return void
     */
    public function copyFromDefault(DUP_PRO_Package $package, DUP_PRO_Package_Upload_Info $upload_info)
    {
        DUP_PRO_Log::infoTrace("Copyng to Storage " . $this->name . '[ID: ' . $this->id . '] type:' . $this->getStypeName());

        $source_archive_filepath   = $package->getLocalPackageFilePath(DUP_PRO_Package_File_Type::Archive);
        $source_installer_filepath = $package->getLocalPackageFilePath(DUP_PRO_Package_File_Type::Installer);

        if ($source_archive_filepath === false) {
            DUP_PRO_Log::traceError("Archive doesn't exist for $package->Name!? - $source_archive_filepath");
            $upload_info->failed = true;
        }

        if ($source_installer_filepath === false) {
            DUP_PRO_Log::traceError("Installer doesn't exist for $package->Name!? - $source_installer_filepath");
            $upload_info->failed = true;
        }

        if ($upload_info->failed == true) {
            DUP_PRO_Log::infoTrace('OneDrive storage failed flag ($upload_info->failed) has been already set.');
            $package->update();
            return;
        }

        $client         = $this->getClient();
        $archive_path   = basename($source_archive_filepath);
        $archive_path   = $this->getStorageFolder() . '/' . $archive_path;
        $installer_name = $package->Installer->getInstallerName();
        $installer_path = $this->getStorageFolder() . '/' . $installer_name;
        try {
            if (!$upload_info->copied_installer) {
                DUP_PRO_Log::trace("ATTEMPT: OneDrive upload installer file $source_installer_filepath to $installer_path");
                $client->uploadFileChunk($source_installer_filepath, $installer_path);
                try {
                    if ($client->RUploader->sha1CheckSum($source_installer_filepath)) {
                        DUP_PRO_Log::infoTrace("SUCCESS: installer upload to OneDrive " . $installer_path);
                        $upload_info->copied_installer = true;
                        $upload_info->progress         = 5;
                        // The package update will automatically capture the upload_info since its part of the package
                        $package->update();
                    } else {
                        DUP_PRO_Log::infoTrace(
                            "FAIL: installer upload to OneDrive $installer_path. " .
                            "The uploaded Uploaded installer file is corrupted, the sha1 hashes don't match!"
                        );
                        $upload_info->increase_failure_count();
                    }
                } catch (Exception $exception) {
                    if ($exception->getCode() == 404 && $client->isBusiness()) {
                        DUP_PRO_Log::infoTrace("SUCCESS: installer upload to OneDrive " . $installer_path);
                        $upload_info->copied_installer = true;
                        $upload_info->progress         = 5;
                        // The package update will automatically capture the upload_info since its part of the package
                        $package->update();
                    } else {
                        DUP_PRO_Log::traceError(
                            "FAIL: installer upload to OneDrive $installer_path. An error occurred while checking the file checksum. Exception message: " .
                            $exception->getMessage()
                        );
                        $upload_info->increase_failure_count();
                    }
                }
            } else {
                DUP_PRO_Log::trace("Already copied installer on previous execution of Onedrive $this->name so skipping");
            }
            if (!$upload_info->copied_archive) {
                /* Delete the archive if we are just starting it (in the event they are pushing another copy */
                if ($upload_info->archive_offset == 0) {
                    DUP_PRO_Log::trace("Archive offset is 0 so try to delete $archive_path");
                    try {
                        $onedrive_archive = $client->fetchDriveItemByPath($archive_path);
                        $client->deleteDriveItem($onedrive_archive->getId());
                    } catch (Exception $ex) {
                        // Burying exceptions
                    }
                }

                $global = DUP_PRO_Global_Entity::getInstance();
                if ($upload_info->data != '' && $upload_info->data2 != '') {
                    $resumable = (object)array(
                        "uploadUrl"      => $upload_info->data,
                        "expirationTime" => $upload_info->data2,
                    );
                    $client->uploadFileChunk(
                        $source_archive_filepath,
                        null,
                        $resumable,
                        $global->php_max_worker_time_in_sec,
                        (50 + $global->getMicrosecLoadReduction()),
                        $upload_info->archive_offset
                    );
                } else {
                    $client->uploadFileChunk(
                        $source_archive_filepath,
                        $archive_path,
                        null,
                        $global->php_max_worker_time_in_sec,
                        (50 + $global->getMicrosecLoadReduction()),
                        $upload_info->archive_offset
                    );
                }

                $onedrive_upload_info = $client->RUploader;
                $upload_info->data    = $onedrive_upload_info->getUploadUrl();
                $upload_info->data2   = $onedrive_upload_info->getExpirationTime();
                if ($onedrive_upload_info->getError() == null) {
                    // Clear the failure count - we are just looking for consecutive errors
                    $upload_info->failure_count  = 0;
                    $upload_info->archive_offset = $onedrive_upload_info->getUploadOffset();
                    $file_size                   = filesize($source_archive_filepath);
                    $upload_info->progress       = max(5, DUP_PRO_U::percentage($upload_info->archive_offset, $file_size, 0));
                    DUP_PRO_Log::infoTrace(
                        "Archive upload offset: $upload_info->archive_offset [File size: $file_size] [Upload progress: $upload_info->progress%]"
                    );
                    if ($onedrive_upload_info->completed()) {
                        try {
                            if ($onedrive_upload_info->sha1CheckSum($source_archive_filepath)) {
                                DUP_PRO_Log::infoTrace("SUCCESS: archive upload to OneDrive.");
                                $upload_info->archive_offset = $file_size;
                                $upload_info->copied_archive = true;
                                $this->purgeOldPackages();
                            } else {
                                DUP_PRO_Log::infoTrace("FAIL: archive upload to OneDrive. sha1 hashes don't match!");
                                $this->setArchiveOffset($upload_info, $onedrive_upload_info);
                                $upload_info->increase_failure_count();
                            }
                        } catch (Exception $exception) {
                            if ($exception->getCode() == 404 && $client->isBusiness()) {
                                DUP_PRO_Log::infoTrace("SUCCESS: archive upload to OneDrive business.");
                                $upload_info->archive_offset = $file_size;
                                $upload_info->copied_archive = true;
                                $this->purgeOldPackages();
                            } else {
                                DUP_PRO_Log::infoTrace(
                                    "FAIL: archive upload to OneDrive. An error occurred while checking the file checksum. Exception message: " .
                                    $exception->getMessage()
                                );
                                $upload_info->increase_failure_count();
                            }
                        }
                    }
                } else {
                    DUP_PRO_Log::traceError(
                        "FAIL: archive upload to OneDrive. An error occurred while checking the file checksum. Error message: " .
                        $onedrive_upload_info->getError()
                    );
                    // error_log("* Else Problem uploading archive for package $package->Name: ".$onedrive_upload_info->getError());

                    // Could have partially uploaded so retain that offset.
                    $this->setArchiveOffset($upload_info, $onedrive_upload_info);
                    $upload_info->increase_failure_count();
                }
            } else {
                DUP_PRO_Log::trace("Already copied archive on previous execution of Onedrive $this->name so skipping");
            }
        } catch (Exception $e) {
            DUP_PRO_Log::trace("Exception caught copying package $package->Name to " . $this->config['storage_folder'] . " " . $e->getMessage());
            $this->setArchiveOffset($upload_info, (isset($onedrive_upload_info) ? $onedrive_upload_info : null));
            $upload_info->increase_failure_count();
        }

        if ($upload_info->failed) {
            DUP_PRO_Log::infoTrace('OneDrive storage failed flag ($upload_info->failed) has been already set.');
        }

        // The package update will automatically capture the upload_info since its part of the package
        $package->update();
    }

    /**
     * Purge old packages
     *
     * @return false|string[] false on failure or array of deleted packages
     */
    public function purgeOldPackages()
    {
        $result = [];
        if ($this->config['max_packages'] <= 0) {
            return $result;
        }

        DUP_PRO_Log::infoTrace("Attempting to purge old packages at " . $this->name . '[ID: ' . $this->id . '] type:' . $this->getSTypeName());

        try {
            $client        = $this->getClient();
            $global        = DUP_PRO_Global_Entity::getInstance();
            $folderId      = $this->config['storage_folder_id'];
            $package_items = $client->fetchDriveItems($folderId);
            $archives      = array();
            $installers    = array();
            foreach ($package_items as $item) {
                $name = $item->getName();
                if (DUP_PRO_STR::endsWith($name, "_{$global->installer_base_name}")) {
                    $installers[] = $item;
                } elseif (DUP_PRO_STR::endsWith($name, '_archive.zip') || DUP_PRO_STR::endsWith($name, '_archive.daf')) {
                    $archives[] = $item;
                }
            }

            $complete_packages = array();
            foreach ($archives as $archive) {
                //$archive_name = pathinfo($archive->getName())["filename"];
                $pathinfo     = pathinfo($archive->getName());
                $archive_name = $pathinfo["filename"];
                DUP_PRO_Log::trace($archive_name . ", looking for installer for this archive.");
                $archive_name = str_replace('_archive', '', $archive_name);
                foreach ($installers as $installer) {
                    //$installer_name = pathinfo($installer->getName())["filename"];
                    $pathinfo = pathinfo($installer->getName());
                    //["filename"];
                    $installer_name = $pathinfo["filename"];
                    DUP_PRO_Log::trace($installer_name);
                    $installer_name = str_replace('_installer', '', $installer_name);
                    if ($archive_name == $installer_name) {
                        DUP_PRO_Log::trace("Found installer for the archive. Adding them to complete_packages.");
                        $complete_packages[] = array(
                            "archive_name" => $archive_name,
                            "archive_id"   => $archive->getId(),
                            "installer_id" => $installer->getId(),
                            "created_time" => $archive->getCreatedTime(),
                        );
                        break;
                    }
                }
            }

            $num_archives           = count($complete_packages);
            $num_archives_to_delete = $num_archives - $this->config['max_packages'];
            DUP_PRO_Log::trace(
                "Num archives files to delete={$num_archives_to_delete} since there are {$num_archives}" .
                " on the drive and max files={$this->config['max_packages']}"
            );

            usort(
                $complete_packages,
                function ($a, $b) {
                    $act = (int)$a['created_time'];
                    $bct = (int)$b['created_time'];
                    if ($act == $bct) {
                        return 0;
                    }

                    return ($act < $bct ? -1 : 1);
                }
            );

            $index = 0;
            while ($index < $num_archives_to_delete) {
                $old_package = $complete_packages[$index];
                DUP_PRO_Log::trace("Deleting old package created on " . $old_package['created_time']);
                $client->deleteDriveItem($old_package["archive_id"]);
                $client->deleteDriveItem($old_package["installer_id"]);
                $index++;
                $result[] = $old_package['archive_name'];
            }
        } catch (Exception $e) {
            DUP_PRO_Log::infoTraceException($e, "FAIL: purge package for storage " . $this->name . '[ID: ' . $this->id . '] type:' . $this->getStypeName());
            return false;
        }

        DUP_PRO_Log::infoTrace("Purge of old packages at " . $this->name . '[ID: ' . $this->id . "] storage completed.");
        return $result;
    }

    /**
     * Get OneDrive client
     *
     * @return false|OneDriveClient
     * @throws Exception
     */
    protected function getClient()
    {
        if (!$this->isValid()) {
            return false;
        }
        if (is_null($this->client)) {
            $scope = DUP_PRO_OneDrive_Config::MSGRAPH_ACCESS_SCOPE;
            $token = new TokenEntity(self::getSType(), [
                'created'       => $this->config['token_obtained'],
                'expires_in'    => 3600,
                'scope'         => $scope,
                'access_token'  => $this->config['access_token'],
                'refresh_token' => $this->config['refresh_token'],
            ]);

            if ($token->isAboutToExpire()) {
                $token->refresh(true);
                if (!$token->isValid()) {
                    return false;
                }
                $this->config['token_obtained'] = $token->getCreated();
                $this->config['refresh_token']  = $token->getRefreshToken();
                $this->config['access_token']   = $token->getAccessToken();
                $this->save();
            }

            $state        = (object) array(
                'redirect_uri' => null,
                'endpoint_url' => $this->config['endpoint_url'],
                'resource_id'  => $this->config['resource_id'],
                'token'        => (object) array(
                    'obtained' => $this->config['token_obtained'],
                    'data'     => (object) [
                        'created'       => $this->config['token_obtained'],
                        'token_type'    => 'bearer',
                        'expires_in'    => 3600,
                        'scope'         => $scope,
                        'access_token'  => $this->config['access_token'],
                        'refresh_token' => $this->config['refresh_token'],
                        'user_id'       => $this->config['user_id'],
                    ],
                ),
            );
            $this->client = DUP_PRO_Onedrive_U::get_onedrive_client_from_state(
                $state,
                true
            );
        }
        return $this->client;
    }

    /**
     * Get account info
     *
     * @return false|object
     */
    protected function getAccountInfo()
    {
        if (($client = $this->getClient()) == false) {
            return false;
        }
        $onedrive_state       = $client->getState();
        $onedrive_state_token = $onedrive_state->token;
        if (isset($onedrive_state_token->data->error)) {
            return false;
        }

        $this->getOneDriveStorageFolder();
        return $client->fetchAccountInfo();
    }

    /**
     * Get state token
     *
     * @return false|object
     */
    protected function getStateToken()
    {
        if (($client = $this->getClient()) == false) {
            return false;
        }
        $onedrive_state = $client->getState();
        return $onedrive_state->token;
    }

    /**
     * Get onedrive storage folder
     *
     * @return OneDriveFolder
     */
    protected function getOneDriveStorageFolder()
    {
        if (!$this->config['storage_folder_id']) {
            $onedrive_folder                   = $this->createOnedriveFolder();
            $this->config['storage_folder_id'] = $onedrive_folder->getId();
            $this->save();
        } else {
            try {
                if (($client = $this->getClient()) == false) {
                    throw new Exception("Can't get OneDrive Client");
                }
                $onedrive_folder_candidate = $client->fetchDriveItem($this->config['storage_folder_id']);
                $onedrive_folder           = $onedrive_folder_candidate;
            } catch (Exception $e) {
                $message = $e->getMessage();
                if (
                    strpos($message, "Item does not exist") !== false ||
                    strpos($message, "The resource could not be found") !== false
                ) {
                    $onedrive_folder                   = $this->createOnedriveFolder();
                    $this->config['storage_folder_id'] = $onedrive_folder->getId();
                    $this->save();
                } else {
                    throw $e;
                }
            }
        }

        return $onedrive_folder;
    }


    /**
     * Check if onedrive folder exists
     *
     * @return bool
     */
    protected function folderExists()
    {
        $folderId = $this->config['storage_folder_id'];
        if (!$folderId) {
            return false;
        } else {
            if (($client = $this->getClient()) == false) {
                throw new Exception("Can't get OneDrive Client");
            }
            $onedrive_folder_candidate = $client->fetchDriveItem($folderId);
            return ($onedrive_folder_candidate) ? true : false;
        }
    }

    /**
     * Create onedrive folder
     *
     * @return OneDriveFolder
     */
    protected function createOnedriveFolder()
    {
        if (($client = $this->getClient()) == false) {
            throw new Exception("Can't get OneDrive Client");
        }

        $parent               = null;
        $current_search_item  = $client->fetchRoot();
        $create_folder        = true;
        $storage_folders_tree = explode("/", $this->config['storage_folder']);
        foreach ($storage_folders_tree as $folder) {
            $child_items = $current_search_item->fetchChildDriveItems();
            DUP_PRO_Log::traceObject("childs", $child_items);
            DUP_PRO_Log::trace("Checking $folder");
            if (!empty($folder)) {
                if (!empty($child_items)) {
                    foreach ($child_items as $item) {
                        if ($item->isFolder()) {
                            $name = $item->getName();
                            DUP_PRO_Log::trace("$folder <===> $name");
                            if ($name == $folder) {
                                $current_search_item = $item;
                                $create_folder       = false;
                                break;
                            }
                        }
                    }
                }
                if ($create_folder) {
                    $new_folder          = $current_search_item->createFolder($folder);
                    $current_search_item = $new_folder;
                }
            }
            $create_folder = true;
        }
        $parent = $current_search_item;
        return $parent;
    }

    /**
     * Set onedrive archive offset
     *
     * @param DUP_PRO_Package_Upload_Info $upload_info          the upload info
     * @param ?ResumableUploader          $onedrive_upload_info OneDrive upload info
     *
     * @return void
     */
    protected function setArchiveOffset(DUP_PRO_Package_Upload_Info $upload_info, $onedrive_upload_info = null)
    {
        DUP_PRO_Log::trace("Try to set OneDrive archive offset because of error");
        if (!empty($upload_info->data)) {
            // error_log("Calling GET resume URL for getting next offset: ".$upload_info->data);
            DUP_PRO_Log::trace("Calling GET resume URL to get OneDrive next offset");
            $archive_offset = '';
            $response       = wp_remote_get($upload_info->data, array('timeout' => 60));
            $response_code  = wp_remote_retrieve_response_code($response);
            // error_log('%% resp code:'. $response_code);
            if (200 == $response_code) {
                $response_body_json = wp_remote_retrieve_body($response);
                /* Will result in $api_response being an array of data,
                parsed from the JSON response of the API listed above */
                $response_body_array = json_decode($response_body_json, true);
                $next_expected_range = isset($response_body_array['nextExpectedRanges'][0])
                    ? trim($response_body_array['nextExpectedRanges'][0], '"')
                    : '';
                // "12345-45754"
                $next_expected_range_parts    = explode('-', $next_expected_range);
                $next_expected_range_parts[0] = intval($next_expected_range_parts[0]);
                if ($next_expected_range_parts[0] > 0) {
                    $archive_offset = $next_expected_range_parts[0];
                    // error_log("Got OneDrive Archive offset $archive_offset from GET resume URL");
                    DUP_PRO_Log::infoTrace("Got OneDrive Archive offset $archive_offset from OneDrive GET resume URL.");
                }
            }
        }

        if (empty($archive_offset)) {
            if (!is_null($onedrive_upload_info)) {
                $archive_offset = $onedrive_upload_info->getUploadOffset();
            } else {
                $archive_offset = 0;
            }
        }

        $upload_info->archive_offset = $archive_offset;
        // error_log("Setting archive offset to the ".$upload_info->archive_offset);
        DUP_PRO_Log::infoTrace("Setting archive offset to the " . $upload_info->archive_offset);
    }

    /**
     * @return void
     * @throws Exception
     */
    public static function registerType()
    {
        parent::registerType();

        add_action('duplicator_update_global_storage_settings', function () {
            $dGlobal = DynamicGlobalEntity::getInstance();

            foreach (static::getDefaultSettings() as $key => $default) {
                $value = SnapUtil::sanitizeIntInput(SnapUtil::INPUT_REQUEST, $key, $default);
                $dGlobal->setVal($key, $value);
            }
            $dGlobal->save();
        });
    }

    /**
     * Get default settings
     *
     * @return array<string, scalar>
     */
    protected static function getDefaultSettings()
    {
        return ['onedrive_upload_chunksize_in_kb' => DUPLICATOR_PRO_ONEDRIVE_UPLOAD_CHUNK_DEFAULT_SIZE_IN_KB];
    }

    /**
     * @return void
     */
    public static function renderGlobalOptions()
    {
        $values  = static::getDefaultSettings();
        $dGlobal = DynamicGlobalEntity::getInstance();
        foreach ($values as $key => $default) {
            $values[$key] = $dGlobal->getVal($key, $default);
        }
        ?>
        <h3 class="title"><?php echo static::getStypeName() ?></h3>
        <hr size="1" />
        <table class="form-table">
            <tr valign="top">
                <th scope="row"><label><?php esc_html_e("Upload Chunk Size", 'duplicator-pro'); ?></label></th>
                <td>
                    <input 
                        type="number"
                        name="onedrive_upload_chunksize_in_kb"
                        id="onedrive_upload_chunksize_in_kb"
                        class="dup-narrow-input text-right"
                        step="320"
                        min="<?php echo intval(DUPLICATOR_PRO_ONEDRIVE_UPLOAD_CHUNK_MIN_SIZE_IN_KB); ?>"
                        data-parsley-required
                        data-parsley-type="number"
                        data-parsley-errors-container="#onedrive_upload_chunksize_in_kb_error_container"
                        value="<?php echo $values['onedrive_upload_chunksize_in_kb']; ?>" 
                    >&nbsp;<b>KB</b>
                    <div id="onedrive_upload_chunksize_in_kb_error_container" class="duplicator-error-container"></div>
                    <p class="description">
                        <?php printf(esc_html__(
                            'How much should be uploaded to OneDrive per attempt. It should be multiple of %dkb. Higher=faster but less reliable.',
                            'duplicator-pro'
                        ), DUPLICATOR_PRO_ONEDRIVE_UPLOAD_CHUNK_MIN_SIZE_IN_KB);
                        // https://docs.microsoft.com/en-us/onedrive/developer/rest-api/api/driveitem_createuploadsession?view=odsp-graph-online#upload-bytes-to-the-upload-session
                        printf(
                            esc_html__('Default size %1$dkb. Min size %2$dkb.', 'duplicator-pro'),
                            DUPLICATOR_PRO_ONEDRIVE_UPLOAD_CHUNK_DEFAULT_SIZE_IN_KB,
                            DUPLICATOR_PRO_ONEDRIVE_UPLOAD_CHUNK_MIN_SIZE_IN_KB
                        ); ?>
                    </p>
                </td>
            </tr>
        </table>
        <?php
    }
}
