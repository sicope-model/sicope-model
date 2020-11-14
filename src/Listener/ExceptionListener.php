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

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Environment;

/**
 * Exception Listener.
 *
 * @author Ramazan APAYDIN <apaydin541@gmail.com>
 */
class ExceptionListener implements EventSubscriberInterface
{
    /**
     * @var Environment
     */
    private $engine;

    public function __construct(Environment $engine)
    {
        $this->engine = $engine;
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        // Get Exception
        $exception = $event->getThrowable();

        if ($exception instanceof NotFoundHttpException) {
            $event->setResponse(new Response($this->engine->render('Admin/_other/notFound.html.twig'), 404));
        }

        if ($exception instanceof AccessDeniedHttpException) {
            $event->setResponse(new Response($this->engine->render('Admin/_other/accessDenied.html.twig'), 403));
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION => [['onKernelException']],
        ];
    }
}
