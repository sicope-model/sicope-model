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

namespace App\Controller;

use Pd\WidgetBundle\Widget\WidgetInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Admin Dashboard.
 *
 * @author Ramazan APAYDIN <apaydin541@gmail.com>
 */
class DashboardController extends AbstractController
{
    /**
     * Dashboard Index.
     *
     * @Route(name="admin_dashboard", path="/")
     * @IsGranted("ROLE_DASHBOARD_PANEL")
     */
    public function index(): Response
    {
        // Render Page
        return $this->render('Admin/dashboard.html.twig');
    }

    /**
     * Change Language for Session.
     *
     * @Route(name="admin_language", path="/language/{lang}")
     */
    public function changeLanguage(Request $request, WidgetInterface $widget, string $lang): RedirectResponse
    {
        // Set Language for Session
        $request->getSession()->set('_locale', $lang);

        // Flush Widget Cache
        $widget->clearWidgetCache();

        // Return Back
        return $this->redirect($request->headers->get('referer', $this->generateUrl('admin_dashboard')));
    }
}
