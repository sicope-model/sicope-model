<?php

/**
 * This file is part of the SICOPE Model package.
 *
 * @package     sicope-model
 * @license     LICENSE
 * @author      Tien Xuan Vo <tien.xuan.vo@gmail.com>
 * @link        https://github.com/sicope-model/sicope-model
 */

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Profiler\Profiler;

class RequestSubscriber implements EventSubscriberInterface
{
    protected ?Profiler $profiler = null;

    public function setProfiler(Profiler $profiler): void
    {
        $this->profiler = $profiler;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            RequestEvent::class => 'onKernelRequest',
        ];
    }

    public function onKernelRequest(RequestEvent $event)
    {
        if ($this->profiler && 'file_manager' === $event->getRequest()->attributes->get('_route')) {
            $this->profiler->disable();
        }
    }
}
