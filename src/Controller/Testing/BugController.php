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

use App\Exception\RuntimeException;
use App\Repository\Testing\BugRepository;
use App\Service\ConfigBag;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Tienvx\Bundle\MbtBundle\Command\CommandPreprocessorInterface;
use Tienvx\Bundle\MbtBundle\Entity\Bug;
use Tienvx\Bundle\MbtBundle\Message\ReduceBugMessage;
use Tienvx\Bundle\MbtBundle\Model\Bug\StepInterface;
use Tienvx\Bundle\MbtBundle\Model\BugInterface;
use Tienvx\Bundle\MbtBundle\Model\Model\Revision\PlaceInterface;
use Tienvx\Bundle\MbtBundle\Model\Model\Revision\TransitionInterface;
use Tienvx\Bundle\MbtBundle\Model\Model\RevisionInterface;
use Tienvx\Bundle\MbtBundle\Model\ModelInterface;
use Tienvx\Bundle\MbtBundle\Provider\ProviderManagerInterface;

/**
 * Controller managing the bugs.
 *
 * @author Tien Xuan Vo <tien.xuan.vo@gmail.com>
 */
class BugController extends AbstractController
{
    protected CommandPreprocessorInterface $commandPreprocessor;

    public function __construct(CommandPreprocessorInterface $commandPreprocessor)
    {
        $this->commandPreprocessor = $commandPreprocessor;
    }

    /**
     * List Bug.
     */
    #[IsGranted('ROLE_BUG_LIST')]
    #[Route('/bugs', name: 'testing.bug_list', methods: ['GET'])]
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
        return $this->render('testing/listBug.html.twig', [
            'bugs' => $pagination,
        ]);
    }

    /**
     * Edit Bug.
     */
    #[IsGranted('ROLE_BUG_EDIT')]
    #[Route('/bug/{bug}/edit', name: 'testing.bug_edit')]
    public function edit(Request $request, Bug $bug, EntityManagerInterface $em): Response
    {
        $form = $this->createFormBuilder($bug)
            ->add('title', TextType::class, [
                'label' => 'testing.task_title',
            ])
            ->add('save', SubmitType::class, [
                'label' => 'save',
            ])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($bug);
            $em->flush();

            // Add Flash
            $this->addFlash('success', 'changes_saved');

            return $this->redirectToRoute('admin_bug_list');
        }

        return $this->render('testing/editBug.html.twig', [
            'page_title' => 'testing.bug_edit_title',
            'page_description' => 'testing.bug_edit_desc',
            'form' => $form->createView(),
        ]);
    }

    /**
     * View Bug.
     */
    #[IsGranted('ROLE_BUG_VIEW')]
    #[Route('/bug/{bug}', name: 'testing.bug_view', methods: ['GET'])]
    public function view(Bug $bug): Response
    {
        return $this->render('testing/viewBug.html.twig', [
            'bug' => $bug,
            'steps' => $this->formatSteps($bug->getTask()->getModelRevision(), ...$bug->getSteps()),
        ]);
    }

    /**
     * Delete Bug.
     */
    #[IsGranted('ROLE_BUG_DELETE')]
    #[Route('/bug/{bug}/delete', name: 'testing.bug_delete', methods: ['DELETE'])]
    public function delete(Request $request, Bug $bug, EntityManagerInterface $em): RedirectResponse
    {
        // Remove
        $em->remove($bug);
        $em->flush();

        // Add Flash
        $this->addFlash('success', 'remove_complete');

        // Redirect back
        return $this->redirect($request->headers->get('referer', $this->generateUrl('admin_bug_list')));
    }

    /**
     * Open Bug.
     */
    #[IsGranted('ROLE_BUG_OPEN')]
    #[Route('/task/{bug}/open', name: 'testing.bug_open')]
    public function open(Request $request, Bug $bug): RedirectResponse
    {
        $bug->setClosed(false);

        return $this->changeStatus($request);
    }

    /**
     * Close Bug.
     */
    #[IsGranted('ROLE_BUG_CLOSE')]
    #[Route('/task/{bug}/close', name: 'testing.bug_close')]
    public function close(Request $request, Bug $bug): RedirectResponse
    {
        $bug->setClosed(true);

        return $this->changeStatus($request);
    }

    /**
     * View Bug Video.
     */
    #[IsGranted('ROLE_BUG_VIDEO')]
    #[Route('/bug/{bug}/video', name: 'testing.bug_video', methods: ['GET'])]
    public function video(Bug $bug, ProviderManagerInterface $providerManager): StreamedResponse
    {
        $providerName = $bug->getTask()->getSeleniumConfig()->getProvider();
        $provider = $providerManager->getProvider($providerName);
        $response = new StreamedResponse();
        $url = $provider->getVideoUrl($providerManager->getSeleniumServer($providerName), $bug->getId());

        $response->headers->set('Content-Type', 'application/force-download');
        $response->headers->set(
            'Content-Disposition',
            $response->headers->makeDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                $provider->getVideoFilename($bug->getId())
            )
        );

        $response->setCallback(function () use ($url) {
            $c = curl_init($url);
            curl_exec($c);
            curl_close($c);
        });

        return $response;
    }

    /**
     * Export Bug.
     */
    #[IsGranted('ROLE_BUG_EXPORT')]
    #[Route('/bug/{bug}/export', name: 'testing.bug_export', methods: ['GET'])]
    public function export(Bug $bug): JsonResponse
    {
        return $this->json($this->formatBug($bug), 200, [
            'Content-Disposition' => HeaderUtils::makeDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                "bug-{$bug->getId()}.side"
            ),
        ])->setEncodingOptions(JsonResponse::DEFAULT_ENCODING_OPTIONS | \JSON_PRETTY_PRINT);
    }

    /**
     * Reduce Bug.
     */
    #[IsGranted('ROLE_BUG_REDUCE')]
    #[Route('/bug/{bug}/reduce', name: 'testing.bug_reduce')]
    public function reduce(Request $request, Bug $bug, MessageBusInterface $messageBus): RedirectResponse
    {
        if ($bug->isReducing()) {
            $this->addFlash('error', 'bug_already_reducing');
        } else {
            $messageBus->dispatch(new ReduceBugMessage($bug->getId()));
            $this->addFlash('success', 'bug_scheduled');
        }

        // Redirect back
        return $this->redirect($request->headers->get('referer', $this->generateUrl('admin_bug_list')));
    }

    /**
     * Convert step object into array for twig rendering.
     */
    protected function formatSteps(RevisionInterface $revision, StepInterface ...$steps): ?array
    {
        return array_map(
            fn (StepInterface $step) => $this->formatStep($revision, $step),
            $steps
        );
    }

    protected function formatStep(RevisionInterface $revision, StepInterface $step): array
    {
        return [
            'transition' => $this->getTransition($revision, $step->getTransition())->getLabel(),
            'places' => array_map(
                fn (int $place) => $this->getPlace($revision, $place)->getLabel(),
                array_keys($step->getPlaces())
            ),
            'color' => $step->getColor()->getValues(),
        ];
    }

    protected function getTransition(RevisionInterface $revision, int $transition): TransitionInterface
    {
        $result = $revision->getTransition($transition);
        if (!$result) {
            // phpcs:ignore Generic.Files.LineLength
            throw new RuntimeException(sprintf('Transition %d does not exist revision %d', $transition, $revision->getId()));
        }

        return $result;
    }

    protected function getPlace(RevisionInterface $revision, int $place): PlaceInterface
    {
        $result = $revision->getPlace($place);
        if (!$result) {
            throw new RuntimeException(sprintf('Place %d does not exist revision %d', $place, $revision->getId()));
        }

        return $result;
    }

    protected function changeStatus(Request $request): RedirectResponse
    {
        $this->getDoctrine()->getManager()->flush();

        // Add Flash
        $this->addFlash('success', 'changes_saved');

        // Redirect back
        return $this->redirect($request->headers->get('referer', $this->generateUrl('admin_bug_list')));
    }

    /**
     * Convert bug object into Selenium IDE project.
     */
    protected function formatBug(BugInterface $bug): array
    {
        return [
            'name' => $this->formatModel($bug->getTask()->getModelRevision()->getModel()),
            'tests' => [
                [
                    'name' => $bug->getTitle(),
                    'commands' => $this->formatCommands($bug->getTask()->getModelRevision(), ...$bug->getSteps()),
                ],
            ],
        ];
    }

    protected function formatModel(?ModelInterface $model): string
    {
        return $model ? $model->getLabel() : '';
    }

    protected function formatCommands(RevisionInterface $revision, StepInterface ...$steps): array
    {
        $formatted = [];
        foreach ($steps as $step) {
            foreach ($this->getTransition($revision, $step->getTransition())->getCommands() as $command) {
                $formatted[] = $this->commandPreprocessor->process($command, $step->getColor())->toArray();
            }
            foreach ($step->getPlaces() as $place => $token) {
                foreach ($this->getPlace($revision, $place)->getCommands() as $command) {
                    $formatted[] = $this->commandPreprocessor->process($command, $step->getColor())->toArray();
                }
            }
        }

        return $formatted;
    }
}
