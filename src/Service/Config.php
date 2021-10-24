<?php

/**
 * This file is part of the SICOPE Model package.
 *
 * @package     sicope-model
 * @license     LICENSE
 * @author      Tien Xuan Vo <tien.xuan.vo@gmail.com>
 * @link        https://github.com/sicope-model/sicope-model
 */

namespace App\Service;

use Craue\ConfigBundle\Util\Config as ConfigUtil;
use Symfony\Component\Form\FormInterface;
use Tienvx\Bundle\MbtBundle\Service\ConfigInterface;

/**
 * Get Config for Testing.
 *
 * @author Tien Xuan Vo <tien.xuan.vo@gmail.com>
 */
class Config implements ConfigInterface
{
    public const GENERATOR = 'generator';
    public const REDUCER = 'reducer';
    public const REPORT_BUG = 'report_bug';
    public const NOTIFY_AUTHOR = 'notify_author';
    public const NOTIFY_CHANNELS = 'notify_channels';
    public const MAX_STEPS = 'max_steps';

    public function __construct(private ConfigUtil $config)
    {
    }

    public function getGenerator(): string
    {
        return (string) $this->config->get(static::GENERATOR);
    }

    public function getReducer(): string
    {
        return (string) $this->config->get(static::REDUCER);
    }

    public function shouldReportBug(): bool
    {
        return (bool) $this->config->get(static::REPORT_BUG);
    }

    public function shouldNotifyAuthor(): bool
    {
        return (bool) $this->config->get(static::NOTIFY_AUTHOR);
    }

    public function getNotifyChannels(): array
    {
        return (array) json_decode($this->config->get(static::NOTIFY_CHANNELS));
    }

    public function getMaxSteps(): int
    {
        return (int) $this->config->get(static::MAX_STEPS);
    }

    public function saveForm(FormInterface $form): void
    {
        $this->config->setMultiple([
            static::GENERATOR => $form->get(static::GENERATOR)->getData(),
            static::REDUCER => $form->get(static::REDUCER)->getData(),
            static::REPORT_BUG => $form->get(static::REPORT_BUG)->getData(),
            static::NOTIFY_AUTHOR => $form->get(static::NOTIFY_AUTHOR)->getData(),
            static::NOTIFY_CHANNELS => json_encode($form->get(static::NOTIFY_CHANNELS)->getData()),
            static::MAX_STEPS => $form->get(static::MAX_STEPS)->getData(),
        ]);
    }

    public function getAll(): array
    {
        $settings = $this->config->all();

        return [
            static::GENERATOR => $settings[static::GENERATOR],
            static::REDUCER => $settings[static::REDUCER],
            static::REPORT_BUG => (bool) $settings[static::REPORT_BUG],
            static::NOTIFY_AUTHOR => (bool) $settings[static::NOTIFY_AUTHOR],
            static::NOTIFY_CHANNELS => (array) json_decode($settings[static::NOTIFY_CHANNELS]),
            static::MAX_STEPS => (int) $settings[static::MAX_STEPS],
        ];
    }
}
