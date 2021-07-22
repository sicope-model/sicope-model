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

namespace App\Controller\Testing;

use App\Form\Testing\ModelImportType;
use App\Form\Testing\ModelType;
use App\Repository\Testing\ModelRepository;
use App\Repository\Testing\TaskRepository;
use App\Service\CommandHelper;
use App\Service\ConfigBag;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Pd\UserBundle\Model\UserInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Process\Process;
use Symfony\Component\Routing\Annotation\Route;
use Tienvx\Bundle\MbtBundle\Entity\Model;
use Tienvx\Bundle\MbtBundle\Factory\Model\RevisionFactory;
use Tienvx\Bundle\MbtBundle\Model\Model\RevisionInterface;
use Tienvx\Bundle\MbtBundle\Model\ModelInterface;
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
     */
    #[IsGranted('ROLE_MODEL_LIST')]
    #[Route('/models', name: 'testing.model_list', methods: ['GET'])]
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
        return $this->render('testing/listModel.html.twig', [
            'models' => $pagination,
        ]);
    }

    /**
     * Build New Model.
     */
    #[IsGranted('ROLE_MODEL_BUILD')]
    #[Route('/models/build', name: 'testing.model_build')]
    public function build(Request $request, EntityManagerInterface $em): Response
    {
        $model = new Model();
        $form = $this->createForm(ModelType::class, $model);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            if ($user instanceof UserInterface) {
                $model->setAuthor($user->getId());
            }
            $em->persist($model);
            $em->flush();

            // Add Flash
            $this->addFlash('success', 'changes_saved');

            return $this->redirectToRoute('admin_model_list');
        }

        return $this->render('testing/editModel.html.twig', [
            'page_title' => 'testing_model_build_title',
            'page_description' => 'testing_model_build_desc',
            'form' => $form->createView(),
        ]);
    }

    /**
     * Edit Model.
     */
    #[IsGranted('ROLE_MODEL_EDIT')]
    #[Route('/model/{model}/edit', name: 'testing.model_edit')]
    public function edit(Request $request, Model $model, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ModelType::class, $model);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $revision = RevisionFactory::createFromArray($model->getActiveRevision()->toArray());
            $model->setActiveRevision($revision);
            $em->persist($model);
            $em->flush();

            // Message
            $this->addFlash('success', 'changes_saved');
        }

        return $this->render('testing/editModel.html.twig', [
            'page_title' => 'testing_model_edit_title',
            'page_description' => 'testing_model_edit_desc',
            'form' => $form->createView(),
        ]);
    }

    /**
     * View Model.
     */
    #[IsGranted('ROLE_MODEL_VIEW')]
    #[Route('/model/{model}', name: 'testing.model_view', methods: ['GET'])]
    public function view(Model $model): Response
    {
        return $this->render('testing/viewModel.html.twig', [
            'model' => $model,
        ]);
    }

    /**
     * Delete Model.
     */
    #[IsGranted('ROLE_MODEL_DELETE')]
    #[Route('/model/{model}/delete', name: 'testing.model_delete', methods: ['DELETE'])]
    public function delete(
        Request $request,
        Model $model,
        EntityManagerInterface $em,
        TaskRepository $taskRepository
    ): RedirectResponse {
        // Remove orphan revisions
        $revisionIds = $model->getRevisions()->map(fn (RevisionInterface $revision) => $revision->getId())->getValues();
        $tasksCount = $taskRepository->countTasksByRevisions($revisionIds);
        foreach ($model->getRevisions() as $revision) {
            if (($tasksCount[$revision->getId()] ?? 0) === 0) {
                $model->removeRevision($revision);
                $em->remove($revision);
            } else {
                $revision->setModel(null);
            }
        }

        // Remove
        $em->remove($model);

        $em->flush();

        // Add Flash
        $this->addFlash('success', 'remove_complete');

        // Redirect back
        return $this->redirect($request->headers->get('referer', $this->generateUrl('admin_model_list')));
    }

    /**
     * View Model Image.
     */
    #[IsGranted('ROLE_MODEL_IMAGE')]
    #[Route('/model/{model}/image', name: 'testing.model_image', methods: ['GET'])]
    public function image(Model $model, CommandHelper $commandHelper, ModelDumper $modelDumper): Response
    {
        $response = new Response();
        if ($commandHelper->verifyCommand('dot')) {
            $process = Process::fromShellCommandline('echo "$DUMP" | dot -Tsvg');
            $process->run(null, ['DUMP' => $modelDumper->dump($model->getActiveRevision())]);

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

    /**
     * Export Model.
     */
    #[IsGranted('ROLE_MODEL_EXPORT')]
    #[Route('/model/{model}/export', name: 'testing.model_export', methods: ['GET'])]
    public function export(Model $model): JsonResponse
    {
        return $this->json($model->toArray(), 200, [
            'Content-Disposition' => HeaderUtils::makeDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                $model->getLabel() . '.json'
            ),
        ])->setEncodingOptions(JsonResponse::DEFAULT_ENCODING_OPTIONS | \JSON_PRETTY_PRINT);
    }

    /**
     * Import Model.
     */
    #[IsGranted('ROLE_MODEL_IMPORT')]
    #[Route('/models/import', name: 'testing.model_import')]
    public function import(Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ModelImportType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var ModelInterface $model */
            $model = $form->get('model')->getData();
            $em->persist($model);
            $em->flush();

            // Add Flash
            $this->addFlash('success', 'changes_saved');

            return $this->redirectToRoute('admin_model_list');
        }

        return $this->render('testing/editModel.html.twig', [
            'page_title' => 'testing_model_import_title',
            'page_description' => 'testing_model_import_desc',
            'form' => $form->createView(),
        ]);
    }
}
