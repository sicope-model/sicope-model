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

use App\Form\Testing\TaskType;
use App\Repository\TaskRepository;
use App\Service\ConfigBag;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Pd\UserBundle\Model\UserInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Tienvx\Bundle\MbtBundle\Entity\Task;

/**
 * Controller managing the tasks.
 *
 * @author Tien Xuan Vo <tien.xuan.vo@gmail.com>
 */
class TaskController extends AbstractController
{
    /**
     * List Task.
     *
     * @IsGranted("ROLE_TASK_LIST")
     * @Route(name="admin_task_list", path="/tasks")
     */
    public function list(
        Request $request,
        TaskRepository $taskRepository,
        ConfigBag $bag,
        PaginatorInterface $paginator
    ): Response {
        // Get Tasks
        $query = $taskRepository->createQueryBuilder('t');

        // Get Result
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            $bag->get('list_count')
        );

        // Render Page
        return $this->render('Admin/Testing/listTask.html.twig', [
            'tasks' => $pagination,
        ]);
    }

    /**
     * Create New Task.
     *
     * @IsGranted("ROLE_TASK_CREATE")
     * @Route(name="admin_task_create", path="/tasks/create")
     */
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

        return $this->render('Admin/Testing/editTask.html.twig', [
            'page_title' => 'testing_task_create_title',
            'page_description' => 'testing_task_create_desc',
            'form' => $form->createView(),
        ]);
    }

    /**
     * Edit Task.
     *
     * @IsGranted("ROLE_TASK_EDIT")
     * @Route(name="admin_task_edit", path="/task/{task}/edit")
     */
    public function edit(Request $request, Task $task, EntityManagerInterface $em): Response
    {
        $form = $this->createFormBuilder($task)
            ->add('title', TextType::class, [
                'label' => 'task_title',
            ])
            ->add('save', SubmitType::class, [
                'label' => 'save',
            ])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($task);
            $em->flush();

            // Add Flash
            $this->addFlash('success', 'changes_saved');

            return $this->redirectToRoute('admin_task_list');
        }

        return $this->render('Admin/Testing/editTask.html.twig', [
            'page_title' => 'testing_task_edit_title',
            'page_description' => 'testing_task_edit_desc',
            'form' => $form->createView(),
        ]);
    }

    /**
     * View Task.
     *
     * @IsGranted("ROLE_TASK_VIEW")
     * @Route(name="admin_task_view", path="/task/{task}")
     */
    public function view(Task $task): Response
    {
        return $this->render('Admin/Testing/viewTask.html.twig', [
            'task' => $task,
        ]);
    }

    /**
     * Delete Task.
     *
     * @IsGranted("ROLE_TASK_DELETE")
     * @Route(name="admin_task_delete", path="/task/{task}/delete")
     */
    public function delete(Request $request, Task $task, EntityManagerInterface $em): RedirectResponse
    {
        // Remove
        $em->remove($task);
        $em->flush();

        // Add Flash
        $this->addFlash('success', 'remove_complete');

        // Redirect back
        return $this->redirect($request->headers->get('referer', $this->generateUrl('admin_task_list')));
    }
}
