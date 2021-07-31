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

use Tienvx\Bundle\MbtBundle\Service\ConfigInterface;

/**
 * Get Config for Testing.
 *
 * @author Tien Xuan Vo <tien.xuan.vo@gmail.com>
 */
class TestingConfig implements ConfigInterface
{
    public function __construct(private ConfigBag $bag)
    {
    }

    public function getGenerator(): string
    {
        return (string) $this->bag->get('generator');
    }

    public function getReducer(): string
    {
        return (string) $this->bag->get('reducer');
    }

    public function shouldReportBug(): bool
    {
        return (bool) $this->bag->get('report_bug');
    }

    public function shouldNotifyAuthor(): bool
    {
        return (bool) $this->bag->get('notify_author');
    }

    public function getNotifyChannels(): array
    {
        return (array) $this->bag->get('notify_channels');
    }

    public function getMaxSteps(): int
    {
        return (int) $this->bag->get('max_steps');
    }
}
