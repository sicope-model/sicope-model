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
    public function list(Request $request, TaskRepository $taskRepository, ConfigBag $bag, PaginatorInterface $paginator): Response
    {
        // Get Models
        $query = $taskRepository->createQueryBuilder('m');

        // Get Result
        $pagination = $paginator->paginate($query,
            $request->query->getInt('page', 1),
            $bag->get('list_count')
        );

        // Render Page
        return $this->render('Admin/Testing/listModel.html.twig', [
            'models' => $pagination,
        ]);
    }

    /**
     * Create New Model.
     *
     * @IsGranted("ROLE_MODEL_CREATE")
     * @Route(name="admin_model_create", path="/model/create")
     */
    public function create(Request $request, EntityManagerInterface $em, TranslatorInterface $translator): Response
    {
        return new Response();
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
