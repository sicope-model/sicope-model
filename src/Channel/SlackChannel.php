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

namespace App\Channel;

use Symfony\Component\Notifier\Bridge\Slack\SlackTransport;
use Tienvx\Bundle\MbtBundle\Channel\AbstractChannel;

class SlackChannel extends AbstractChannel
{
    public static function getName(): string
    {
        return 'chat/slack';
    }

    public static function isSupported(): bool
    {
        return class_exists(SlackTransport::class);
    }
}
