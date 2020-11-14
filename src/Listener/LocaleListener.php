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

namespace App\Listener;

use App\Service\ConfigBag;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Change System Default Language.
 *
 * @author Ramazan APAYDIN <apaydin541@gmail.com>
 */
class LocaleListener implements EventSubscriberInterface
{
    /**
     * @var ConfigBag
     */
    private $bag;

    public function __construct(ConfigBag $bag)
    {
        $this->bag = $bag;
    }

    public function setDefaultLocale(KernelEvent $event): void
    {
        $event->getRequest()->setDefaultLocale($this->bag->get('default_locale'));
    }

    public static function getSubscribedEvents()
    {
        return [KernelEvents::REQUEST => [['setDefaultLocale', 99]]];
    }
}
