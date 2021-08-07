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

namespace App\Controller;

use App\Form\ConfigForm;
use App\Service\ConfigBag;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller managing the testing config.
 *
 * @author Tien Xuan Vo <tien.xuan.vo@gmail.com>
 */
class ConfigController extends AbstractController
{
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/config', name: 'app_config')]
    public function config(Request $request, ConfigBag $bag): Response
    {
        // Create Form
        $form = $this->createForm(ConfigForm::class, $bag->getAll());

        // Handle Request
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $bag->saveForm($form);
            $this->addFlash('success', 'message.saved');

            return $this->redirectToRoute('app_config');
        }

        // Render Page
        return $this->render('config.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
