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

use App\Form\Testing\TaskType;
use App\Repository\Testing\TaskRepository;
use App\Service\ConfigBag;
use App\Tables\TaskListTable;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Pd\UserBundle\Model\UserInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Tienvx\Bundle\MbtBundle\Entity\Task;
use Tienvx\Bundle\MbtBundle\Message\RunTaskMessage;

/**
 * Controller managing the tasks.
 *
 * @author Tien Xuan Vo <tien.xuan.vo@gmail.com>
 */
class TaskController extends AbstractController
{
    public function __construct(private ConfigBag $bag)
    {
    }

    /**
     * List Task.
     */
    #[IsGranted('ROLE_TASK_LIST')]
    #[Route('/tasks', name: 'testing.task_list', methods: ['GET'])]
    public function list(
        Request $request,
        TaskListTable $table,
        TaskRepository $taskRepo,
        PaginatorInterface $paginator
    ): Response {
        $table
            ->handleQueryBuilder($query = $taskRepo->createQueryBuilder('t'))
            ->handleRequest($request);

        // Paginate
        $pagination = $paginator->paginate($query->getQuery(),
            $request->query->getInt('page', 1),
            $this->bag->get('list_count')
        );

        if ($request->get('export')) {
            return $table->export();
        }

        return $request->isXmlHttpRequest() ?
            $this->json($pagination, context: ['groups' => 'default']) :
            $this->render('testing/listTask.html.twig', ['table' => $table]);
    }

    /**
     * Create New Task.
     */
    #[IsGranted('ROLE_TASK_CREATE')]
    #[Route('/tasks/create', name: 'testing.task_create')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            if ($user instanceof UserInterface) {
                $task->setAuthor($user->getId());
            }
            $em->persist($task);
            $em->flush();

            // Add Flash
            $this->addFlash('success', 'changes_saved');

            return $this->redirectToRoute('admin_task_list');
        }

        return $this->render('testing/editTask.html.twig', [
            'page_title' => 'testing.task_create_title',
            'page_description' => 'testing.task_create_desc',
            'form' => $form->createView(),
        ]);
    }

    /**
     * Edit Task.
     */
    #[IsGranted('ROLE_TASK_EDIT')]
    #[Route('/task/{task}/edit', name: 'testing.task_edit')]
    public function edit(Request $request, Task $task, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($task);
            $em->flush();

            // Add Flash
            $this->addFlash('success', 'changes_saved');
            $this->addFlash('notice', 'running_tasks_notice');

            return $this->redirectToRoute('admin_task_list');
        }

        return $this->render('testing/editTask.html.twig', [
            'page_title' => 'testing.task_edit_title',
            'page_description' => 'testing.task_edit_desc',
            'form' => $form->createView(),
        ]);
    }

    /**
     * View Task.
     */
    #[IsGranted('ROLE_TASK_VIEW')]
    #[Route('/task/{task}', name: 'testing.task_view', methods: ['GET'])]
    public function view(Task $task): Response
    {
        return $this->render('testing/viewTask.html.twig', [
            'task' => $task,
        ]);
    }

    /**
     * Delete Task.
     */
    #[IsGranted('ROLE_TASK_DELETE')]
    #[Route('/task/{task}/delete', name: 'testing.task_delete', methods: ['DELETE'])]
    public function delete(Request $request, Task $task, EntityManagerInterface $em): RedirectResponse
    {
        // Remove
        $em->remove($task);

        // Remove orphan model revision.
        $revision = $task->getModelRevision();
        if (!$revision->getModel()) {
            $em->remove($revision);
        }

        $em->flush();

        // Add Flash
        $this->addFlash('success', 'remove_complete');

        // Redirect back
        return $this->redirect($request->headers->get('referer', $this->generateUrl('admin_task_list')));
    }

    /**
     * Run Task.
     */
    #[IsGranted('ROLE_TASK_RUN')]
    #[Route('/task/{task}/run', name: 'testing.task_run')]
    public function run(Request $request, Task $task, MessageBusInterface $messageBus): RedirectResponse
    {
        if ($task->isRunning()) {
            $this->addFlash('error', 'task_already_running');
        } else {
            $messageBus->dispatch(new RunTaskMessage($task->getId()));
            $this->addFlash('success', 'task_scheduled');
        }

        // Redirect back
        return $this->redirect($request->headers->get('referer', $this->generateUrl('admin_task_list')));
    }
}
