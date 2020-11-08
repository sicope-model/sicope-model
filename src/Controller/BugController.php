<?php

namespace App\Controller;

use App\Entity\Account\Group;
use App\Form\Account\RolesType;
use App\Menu\GroupsMenu;
use App\Repository\GroupRepository;
use App\Repository\TaskRepository;
use App\Service\ConfigBag;
use App\Service\SecurityService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Pd\UserBundle\Form\GroupType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tienvx\Bundle\MbtBundle\Entity\Model;

/**
 * Controller managing the bugs.
 *
 * @author Tien Xuan Vo <tien.xuan.vo@gmail.com>
 */
class BugController extends AbstractController
{
    /**
     * List Bug.
     *
     * @IsGranted("ROLE_BUG_LIST")
     * @Route(name="admin_bug_list", path="/bug")
     */
    public function list(Request $request, TaskRepository $taskRepository, ConfigBag $bag, PaginatorInterface $paginator): Response
    {
        // Get Bugs
        $query = $taskRepository->createQueryBuilder('b');

        // Get Result
        $pagination = $paginator->paginate($query,
            $request->query->getInt('page', 1),
            $bag->get('list_count')
        );

        // Render Page
        return $this->render('Admin/Testing/listBug.html.twig', [
            'models' => $pagination,
        ]);
    }
}
