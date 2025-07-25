<?php

/**
 *
 * @package   Duplicator
 * @copyright (c) 2022, Snap Creek LLC
 */

namespace Duplicator\Addons\ProBase;

use Duplicator\Addons\ProBase\License\License;

class DrmHandler
{
    const SCHEDULE_DRM_DELAY_DAYS = 14;

    /**
     * Return DRM activation days
     *
     * @return int -1 if has already expired, days left otherwise
     */
    public static function getDaysTillDRM()
    {
        $status = License::getLicenseStatus();
        if ($status !== License::STATUS_VALID && $status !== License::STATUS_EXPIRED) {
            return -1;
        }
        if (($expiresDays = License::getExpirationDays()) === false) {
            return -1;
        }
        return (self::SCHEDULE_DRM_DELAY_DAYS + $expiresDays);
    }
}
