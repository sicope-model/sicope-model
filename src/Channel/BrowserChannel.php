<?php

/**
 * This file is part of the SICOPE Model package.
 *
 * @package     sicope-model
 * @license     LICENSE
 * @author      Ramazan APAYDIN <apaydin541@gmail.com>
 * @author      Tien Xuan Vo <tien.xuan.vo@gmail.com>
 * @link        https://github.com/sicope-model/sicope-model
 */

namespace App\Channel;

use Tienvx\Bundle\MbtBundle\Channel\AbstractChannel;

class BrowserChannel extends AbstractChannel
{
    public static function getName(): string
    {
        return 'browser';
    }

    public static function isSupported(): bool
    {
        return true;
    }
}
