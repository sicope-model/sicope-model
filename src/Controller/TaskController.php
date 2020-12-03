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

use App\Repository\TaskRepository;
use App\Service\ConfigBag;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

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
     * @Route(name="admin_task_list", path="/task")
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
     * @Route(name="admin_task_create", path="/task/create")
     */
    public function create(Request $request, EntityManagerInterface $em, TranslatorInterface $translator): Response
    {
        return new Response();
    }
}
