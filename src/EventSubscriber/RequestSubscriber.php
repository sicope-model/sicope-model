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

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\DependencyInjection\Attribute\When;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Profiler\Profiler;

#[When(env: 'dev')]
#[When(env: 'test')]
class RequestSubscriber implements EventSubscriberInterface
{
    public function __construct(
        #[Autowire(service: 'profiler')]
        protected Profiler $profiler
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            RequestEvent::class => 'onKernelRequest',
        ];
    }

    public function onKernelRequest(RequestEvent $event)
    {
        if ('file_manager' === $event->getRequest()->attributes->get('_route')) {
            $this->profiler->disable();
        }
    }
}
