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
use App\Service\CommandHelper;
use App\Service\ConfigBag;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Process\Process;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tienvx\Bundle\MbtBundle\Entity\Model;
use Tienvx\Bundle\MbtBundle\Service\Model\ModelDumper;

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
    public function build(Request $request, EntityManagerInterface $em): Response
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
     * Edit Model.
     *
     * @IsGranted("ROLE_MODEL_EDIT")
     * @Route(name="admin_model_edit", path="/model/{model}")
     */
    public function edit(Request $request, Model $model, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ModelType::class, $model);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($model);
            $em->flush();

            // Message
            $this->addFlash('success', 'changes_saved');
        }

        return $this->render('Admin/Testing/buildModel.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Delete Model.
     *
     * @IsGranted("ROLE_MODEL_DELETE")
     * @Route(name="admin_model_delete", path="/group/{model}/delete")
     */
    public function delete(Request $request, Model $model, EntityManagerInterface $em): RedirectResponse
    {
        // Remove
        $em->remove($model);
        $em->flush();

        // Add Flash
        $this->addFlash('success', 'changes_saved');

        // Redirect back
        return $this->redirect($request->headers->get('referer', $this->generateUrl('admin_model_list')));
    }

    /**
     * View Model Image.
     *
     * @IsGranted("ROLE_MODEL_IMAGE")
     * @Route(name="admin_model_image", path="/model/{model}/image")
     */
    public function image(Model $model, CommandHelper $commandHelper, ModelDumper $modelDumper): Response
    {
        $response = new Response();
        if ($commandHelper->verifyCommand('dot')) {
            $process = Process::fromShellCommandline('echo "$DUMP" | dot -Tsvg');
            $process->run(null, ['DUMP' => $modelDumper->dump($model)]);

            $disposition = $response->headers->makeDisposition(
                ResponseHeaderBag::DISPOSITION_INLINE,
                $model->getLabel() . '.svg'
            );
            $response->headers->set('Content-Disposition', $disposition);
            $response->headers->set('Content-Type', 'image/svg+xml');
            $response->setContent($process->getOutput());
        }

        return $response;
    }
}
