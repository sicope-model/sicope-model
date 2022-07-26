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
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
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
    public const EMAIL_SENDER = 'email_sender';
    public const MAX_STEPS = 'max_steps';
    public const CREATE_NEW_BUG_WHILE_REDUCING = 'create_new_bug_while_reducing';
    public const DEFAULT_BUG_TITLE = 'default_bug_title';

    public function __construct(
        protected ConfigUtil $config,
        protected ParameterBagInterface $params
    ) {
    }

    public function getGenerator(): string
    {
        return $this->get(static::GENERATOR);
    }

    public function getReducer(): string
    {
        return $this->get(static::REDUCER);
    }

    public function shouldReportBug(): bool
    {
        return (bool) $this->get(static::REPORT_BUG);
    }

    public function shouldNotifyAuthor(): bool
    {
        return (bool) $this->get(static::NOTIFY_AUTHOR);
    }

    public function getNotifyChannels(): array
    {
        return (array) json_decode($this->get(static::NOTIFY_CHANNELS));
    }

    public function getEmailSender(): string
    {
        return $this->get(static::EMAIL_SENDER);
    }

    public function getMaxSteps(): int
    {
        return (int) $this->get(static::MAX_STEPS);
    }

    public function shouldCreateNewBugWhileReducing(): bool
    {
        return (bool) $this->get(static::CREATE_NEW_BUG_WHILE_REDUCING);
    }

    public function getDefaultBugTitle(): string
    {
        return $this->get(static::DEFAULT_BUG_TITLE);
    }

    public function saveForm(FormInterface $form): void
    {
        $this->config->setMultiple([
            static::GENERATOR => $form->get(static::GENERATOR)->getData(),
            static::REDUCER => $form->get(static::REDUCER)->getData(),
            static::REPORT_BUG => $form->get(static::REPORT_BUG)->getData(),
            static::NOTIFY_AUTHOR => $form->get(static::NOTIFY_AUTHOR)->getData(),
            static::NOTIFY_CHANNELS => json_encode($form->get(static::NOTIFY_CHANNELS)->getData()),
            static::EMAIL_SENDER => $form->get(static::EMAIL_SENDER)->getData(),
            static::MAX_STEPS => $form->get(static::MAX_STEPS)->getData(),
            static::CREATE_NEW_BUG_WHILE_REDUCING => $form->get(static::CREATE_NEW_BUG_WHILE_REDUCING)->getData(),
            static::DEFAULT_BUG_TITLE => $form->get(static::DEFAULT_BUG_TITLE)->getData(),
        ]);
    }

    public function getAll(): array
    {
        $all = $this->config->all();

        return [
            static::GENERATOR => $this->get(static::GENERATOR, $all),
            static::REDUCER => $this->get(static::REDUCER, $all),
            static::REPORT_BUG => (bool) $this->get(static::REPORT_BUG, $all),
            static::NOTIFY_AUTHOR => (bool) $this->get(static::NOTIFY_AUTHOR, $all),
            static::NOTIFY_CHANNELS => (array) json_decode($this->get(static::NOTIFY_CHANNELS, $all)),
            static::EMAIL_SENDER => $this->get(static::EMAIL_SENDER, $all),
            static::MAX_STEPS => (int) $this->get(static::MAX_STEPS, $all),
            static::CREATE_NEW_BUG_WHILE_REDUCING => (bool) $this->get(static::CREATE_NEW_BUG_WHILE_REDUCING, $all),
            static::DEFAULT_BUG_TITLE => $this->get(static::DEFAULT_BUG_TITLE, $all),
        ];
    }

    protected function get(string $key, ?array $all = null): string
    {
        if (\is_array($all)) {
            return $all[$key] ?? $this->getParam($key);
        }

        try {
            return (string) $this->config->get($key);
        } catch (\RuntimeException $e) {
            return $this->getParam($key);
        }
    }

    protected function getParam(string $key): string
    {
        return (string) $this->params->get(sprintf('app.%s', $key));
    }
}
