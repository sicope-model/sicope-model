<?php

/**
 * This file is part of the SICOPE Model package.
 *
 * @package     sicope-model
 * @license     LICENSE
 * @author      Ramazan APAYDIN <apaydin541@gmail.com>
 * @link        https://github.com/appaydin/pd-admin
 * @author      Tien Xuan Vo <tien.xuan.vo@gmail.com>
 * @link        https://github.com/sicope-model/sicope-model
 */

namespace App\Service;

/**
 * Tools.
 *
 * @author Ramazan APAYDIN <apaydin541@gmail.com>
 */
class Tools
{
    /**
     * Get Project Root Directory.
     */
    public static function rootDir(string $path = ''): string
    {
        return \dirname(__DIR__, 2) . '/' . $path;
    }

    /**
     * Get Upload Dir.
     */
    public static function uploadDir(string $file = ''): string
    {
        return self::rootDir($_ENV['UPLOAD_DIR'] . $file);
    }
}
