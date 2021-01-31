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

use App\Form\Testing\ModelImportType;
use App\Form\Testing\ModelType;
use App\Repository\ModelRepository;
use App\Repository\TaskRepository;
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
use Tienvx\Bundle\MbtBundle\Entity\Task;
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
     *
     * @IsGranted("ROLE_MODEL_LIST")
     * @Route(name="admin_model_list", path="/models")
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
     * @Route(name="admin_model_build", path="/models/build")
     *
     * @return RedirectResponse|Response
     */
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

        return $this->render('Admin/Testing/editModel.html.twig', [
            'page_title' => 'testing_model_build_title',
            'page_description' => 'testing_model_build_desc',
            'form' => $form->createView(),
        ]);
    }

    /**
     * Edit Model.
     *
     * @IsGranted("ROLE_MODEL_EDIT")
     * @Route(name="admin_model_edit", path="/model/{model}/edit")
     */
    public function edit(Request $request, Model $model, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ModelType::class, $model);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $oldRevision = $model->getActiveRevision();
            $oldRevision->setModel(null);
            $revision = RevisionFactory::createFromArray($oldRevision->toArray());
            $model->setActiveRevision($revision);
            $em->persist($model);
            $em->flush();

            // Message
            $this->addFlash('success', 'changes_saved');
        }

        return $this->render('Admin/Testing/editModel.html.twig', [
            'page_title' => 'testing_model_edit_title',
            'page_description' => 'testing_model_edit_desc',
            'form' => $form->createView(),
        ]);
    }

    /**
     * View Model.
     *
     * @IsGranted("ROLE_MODEL_VIEW")
     * @Route(name="admin_model_view", path="/model/{model}")
     */
    public function view(Model $model): Response
    {
        return $this->render('Admin/Testing/viewModel.html.twig', [
            'model' => $model,
        ]);
    }

    /**
     * Delete Model.
     *
     * @IsGranted("ROLE_MODEL_DELETE")
     * @Route(name="admin_model_delete", path="/model/{model}/delete")
     */
    public function delete(Request $request, Model $model, EntityManagerInterface $em): RedirectResponse
    {
        // Remove
        $em->remove($model);

        // Remove orphan revisions
        $revisionIds = $model->getRevisions()->map(fn (RevisionInterface $revision) => $revision->getId())->getValues();
        /** @var TaskRepository $taskRepository */
        $taskRepository = $em->getRepository(Task::class);
        $tasksCount = $taskRepository->countTasksByRevisions($revisionIds);
        foreach ($model->getRevisions() as $revision) {
            if (($tasksCount[$revision->getId()] ?? 0) === 0) {
                $em->remove($revision);
            } else {
                $revision->setModel(null);
            }
        }

        $em->flush();

        // Add Flash
        $this->addFlash('success', 'remove_complete');

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

    /**
     * Export Model.
     *
     * @IsGranted("ROLE_MODEL_EXPORT")
     * @Route(name="admin_model_export", path="/model/{model}/export")
     */
    public function export(Model $model): JsonResponse
    {
        return $this->json($model->normalize(), 200, [
            'Content-Disposition' => HeaderUtils::makeDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                $model->getLabel() . '.json'
            ),
        ])->setEncodingOptions(JsonResponse::DEFAULT_ENCODING_OPTIONS | \JSON_PRETTY_PRINT);
    }

    /**
     * Import Model.
     *
     * @IsGranted("ROLE_MODEL_IMPORT")
     * @Route(name="admin_model_import", path="/models/import")
     *
     * @return RedirectResponse|Response
     */
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

        return $this->render('Admin/Testing/editModel.html.twig', [
            'page_title' => 'testing_model_import_title',
            'page_description' => 'testing_model_import_desc',
            'form' => $form->createView(),
        ]);
    }
}
