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

use App\Repository\BugRepository;
use App\Service\ConfigBag;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Tienvx\Bundle\MbtBundle\Entity\Bug;

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
    public function list(
        Request $request,
        BugRepository $bugRepository,
        ConfigBag $bag,
        PaginatorInterface $paginator
    ): Response {
        // Get Bugs
        $query = $bugRepository->createQueryBuilder('b');

        // Get Result
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            $bag->get('list_count')
        );

        // Render Page
        return $this->render('Admin/Testing/listBug.html.twig', [
            'bugs' => $pagination,
        ]);
    }

    /**
     * Edit Bug.
     *
     * @IsGranted("ROLE_BUG_EDIT")
     * @Route(name="admin_bug_edit", path="/bug/{bug}")
     */
    public function edit(Request $request, Bug $bug, EntityManagerInterface $em): Response
    {
        $form = $this->createFormBuilder($bug)
            ->add('title', TextType::class, [
                'label' => 'task_title',
            ])
            ->add('save', SubmitType::class, [
                'label' => 'save',
            ])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($bug);
            $em->flush();

            return $this->redirectToRoute('admin_bug_list');
        }

        return $this->render('Admin/Testing/bug.html.twig', [
            'page_title' => 'testing_bug_edit_title',
            'page_description' => 'testing_bug_edit_desc',
            'form' => $form->createView(),
        ]);
    }

    /**
     * Delete Bug.
     *
     * @IsGranted("ROLE_BUG_DELETE")
     * @Route(name="admin_bug_delete", path="/bug/{bug}/delete")
     */
    public function delete(Request $request, Bug $bug, EntityManagerInterface $em): RedirectResponse
    {
        // Remove
        $em->remove($bug);
        $em->flush();

        // Add Flash
        $this->addFlash('success', 'changes_saved');

        // Redirect back
        return $this->redirect($request->headers->get('referer', $this->generateUrl('admin_bug_list')));
    }
}
