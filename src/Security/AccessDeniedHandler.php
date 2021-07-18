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

namespace App\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class AccessDeniedHandler implements AccessDeniedHandlerInterface
{
    private TranslatorInterface $translator;
    private RouterInterface $router;

    public function __construct(TranslatorInterface $translator, RouterInterface $router)
    {
        $this->translator = $translator;
        $this->router = $router;
    }

    public function handle(Request $request, AccessDeniedException $accessDeniedException): RedirectResponse | JsonResponse
    {
        // Create Message
        $message = $accessDeniedException->getMessage();
        switch ($message) {
            case false !== mb_stristr($message, '@IsGranted'):
                $message = $this->translator->trans('access_denied_not_authorized', [], 'acl');
                break;
            case false !== mb_stristr($message, 'Access Denied.'):
                $message = $this->translator->trans('access_denied', [], 'acl');
                break;
            default:
                $message = $this->translator->trans($message);
        }

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse([
                'messages' => [
                    'danger' => [$message],
                ],
            ], $accessDeniedException->getCode());
        }

        // Set Flash Message
        $request->getSession()->getBag('flashes')->add('danger', $message);

        // Send Response
        return new RedirectResponse($request->headers->get('referer', $this->router->generate('security_login')));
    }
}
