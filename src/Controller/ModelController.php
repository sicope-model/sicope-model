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

use App\Form\Testing\ModelType;
use App\Repository\ModelRepository;
use App\Repository\TaskRepository;
use App\Service\ConfigBag;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tienvx\Bundle\MbtBundle\Entity\Model;

/**
 * Controller managing the models.
 *
 * @author Tien Xuan Vo <tien.xuan.vo@gmail.com>
 */
class ModelController extends AbstractController
{
    /**
     * List Model.
     *
     * @IsGranted("ROLE_MODEL_LIST")
     * @Route(name="admin_model_list", path="/model")
     */
    public function list(
        Request $request,
        ModelRepository $modelRepository,
        ConfigBag $bag,
        PaginatorInterface $paginator
    ): Response {
        // Get Models
        $query = $modelRepository->createQueryBuilder('m');

        // Get Result
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            $bag->get('list_count')
        );

        // Render Page
        return $this->render('Admin/Testing/listModel.html.twig', [
            'models' => $pagination,
        ]);
    }

    /**
     * Build New Model.
     *
     * @IsGranted("ROLE_MODEL_BUILD")
     * @Route(name="admin_model_build", path="/model-build")
     *
     * @return RedirectResponse|Response
     */
    public function build(Request $request, EntityManagerInterface $em, TranslatorInterface $translator): Response
    {
        $model = new Model();
        $form = $this->createForm(ModelType::class, $model);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($model);
            $em->flush();

            return $this->redirectToRoute('admin_model_list');
        }

        return $this->render('Admin/Testing/buildModel.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * View Model.
     *
     * @IsGranted("ROLE_MODEL_VIEW")
     * @Route(name="admin_model_view", path="/model/{model}")
     */
    public function view(Request $request, EntityManagerInterface $em, Model $model): Response
    {
        return new Response();
    }

    /**
     * View Model Image.
     *
     * @IsGranted("ROLE_MODEL_IMAGE")
     * @Route(name="admin_model_image", path="/model/{model}/image")
     */
    public function image(Request $request, EntityManagerInterface $em, Model $model): Response
    {
        return new Response();
    }
}
