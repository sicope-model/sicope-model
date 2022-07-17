<?php

/**
 * This file is part of the SICOPE Model package.
 *
 * @package     sicope-model
 * @license     LICENSE
 * @author      Tien Xuan Vo <tien.xuan.vo@gmail.com>
 * @link        https://github.com/sicope-model/sicope-model
 */

namespace App\Controller;

use App\Entity\Message;
use App\Entity\User;
use App\Repository\MessageRepository;
use App\Service\SessionHelper;
use Craue\ConfigBundle\Entity\Setting;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;
use Tienvx\Bundle\MbtBundle\Entity\Bug;
use Tienvx\Bundle\MbtBundle\Entity\Model;
use Tienvx\Bundle\MbtBundle\Entity\Task;

class DashboardController extends AbstractDashboardController
{
    private const TIME = '-12 Day';
    private const COLUMNS = 12;
    private const OPTIONS = [
        'animation' => [
            'duration' => 2000,
            'easing' => 'easeOutQuart',
        ],
        'plugins' => [
            'legend' => [
                'display' => false,
                'position' => 'top',
            ],
            'title' => [
                'display' => false,
                'position' => 'left',
            ],
        ],
        'scales' => [
            'yAxes' => [
                [
                    'ticks' => [
                        'beginAtZero' => true,
                        'stepSize' => 1,
                    ],
                ],
            ],
        ],
    ];

    public function __construct(
        private ChartBuilderInterface $chartBuilder,
        private EntityManagerInterface $entityManager,
        private SessionHelper $sessionHelper,
        private MessageRepository $messageRepository
    ) {
    }

    #[Route('/', name: 'app_dashboard')]
    public function index(): Response
    {
        return $this->render('dashboard.html.twig', [
            'overviewChart' => $this->getOverviewChart(),
            'sessionChart' => $this->getSessionChart(),
            'messageChart' => $this->getMessageChart(),
            'bugs' => $this->getCount(Bug::class),
            'tasks' => $this->getCount(Task::class),
            'models' => $this->getCount(Model::class),
            'users' => $this->getCount(User::class),
            'sessions' => $this->sessionHelper->getSessionStatistics(),
        ]);
    }

    #[Route('/status', name: 'app_status')]
    public function status(): JsonResponse
    {
        return new JsonResponse([
            'sessions' => $this->sessionHelper->getSessionStatistics(),
            'messages' => [
                'all' => $this->messageRepository->countAll(),
                'errors' => $this->messageRepository->countErrors(),
            ],
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('SICOPE Model')
            ->setFaviconPath('build/favicon.ico')
            ->renderContentMaximized();
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Tasks', 'fa fa-tasks', Task::class);
        yield MenuItem::linkToCrud('Models', 'fa fa-project-diagram', Model::class);
        yield MenuItem::linkToCrud('Bugs', 'fa fa-bug', Bug::class);
        yield MenuItem::linkToCrud('Users', 'fa fa-users', User::class)
            ->setPermission('ROLE_ADMIN');
        yield MenuItem::linkToCrud('Errors', 'fa fa-exclamation-circle', Message::class)
            ->setPermission('ROLE_ADMIN');
        yield MenuItem::linkToCrud('Config', 'fa fa-cogs', Setting::class)
            ->setPermission('ROLE_ADMIN')
            ->setAction(Action::EDIT);
        yield MenuItem::linkToRoute('Files', 'fa fa-file', 'app_files');
    }

    private function getOverviewChart(): Chart
    {
        $date = new \DateTime(static::TIME);

        // Load Records
        $taskData = $this->entityManager->getRepository(Task::class)
            ->createQueryBuilder('t')
            ->select('count(t.id) as count, DAY(t.createdAt) as day')
            ->groupBy('day')
            ->where('t.createdAt >= :date')
            ->setParameter('date', $date)
            ->getQuery()->getArrayResult();
        $bugData = $this->entityManager->getRepository(Bug::class)
            ->createQueryBuilder('b')
            ->select('count(b.id) as count, DAY(b.createdAt) as day')
            ->groupBy('day')
            ->where('b.createdAt >= :date')
            ->setParameter('date', $date)
            ->getQuery()->getArrayResult();
        $modelData = $this->entityManager->getRepository(Model::class)
            ->createQueryBuilder('m')
            ->select('count(m.id) as count, DAY(m.createdAt) as day')
            ->groupBy('day')
            ->where('m.createdAt >= :date')
            ->setParameter('date', $date)
            ->getQuery()->getArrayResult();
        $taskData = array_column($taskData, 'count', 'day');
        $bugData = array_column($bugData, 'count', 'day');
        $modelData = array_column($modelData, 'count', 'day');

        // Optimize Data
        $labels = $tasks = $bugs = $models = [];
        for ($i = static::COLUMNS - 1; $i >= 0; --$i) {
            list($day, $month) = explode('/', date('j/m', strtotime("-{$i} day")));
            $labels[] = $day . '/' . $month;
            $tasks[] = $taskData[$day] ?? 0;
            $bugs[] = $bugData[$day] ?? 0;
            $models[] = $modelData[$day] ?? 0;
        }

        $chart = $this->chartBuilder->createChart(Chart::TYPE_BAR);
        $chart->setData([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Tasks',
                    'backgroundColor' => 'rgba(48, 164, 255, 0.2)',
                    'borderColor' => 'rgba(48, 164, 255, 0.8)',
                    'fill' => true,
                    'data' => $tasks,
                ],
                [
                    'label' => 'Bugs',
                    'backgroundColor' => 'rgb(255, 99, 132, 0.2)',
                    'borderColor' => 'rgb(255, 99, 132, 0.8)',
                    'fill' => true,
                    'data' => $bugs,
                ],
                [
                    'label' => 'Models',
                    'backgroundColor' => 'rgba(76, 175, 80, 0.5)',
                    'borderColor' => '#6da252',
                    'fill' => true,
                    'data' => $models,
                ],
            ],
        ]);

        $chart->setOptions(static::OPTIONS);

        return $chart;
    }

    private function getSessionChart(): Chart
    {
        $chart = $this->chartBuilder->createChart(Chart::TYPE_LINE);
        $chart->setData([
            'labels' => [],
            'datasets' => [
                [
                    'label' => 'Total',
                    'data' => [],
                    'fill' => true,
                    'borderWidth' => 1,
                    'borderColor' => '#00b5ad',
                ],
                [
                    'label' => 'Used',
                    'data' => [],
                    'fill' => true,
                    'borderWidth' => 1,
                    'borderColor' => '#b5cc18',
                ],
                [
                    'label' => 'Queued',
                    'data' => [],
                    'fill' => true,
                    'borderWidth' => 1,
                    'borderColor' => '#6435c9',
                ],
                [
                    'label' => 'Pending',
                    'data' => [],
                    'fill' => true,
                    'borderWidth' => 1,
                    'borderColor' => '#f2711c',
                ],
            ],
        ]);

        $chart->setOptions(static::OPTIONS);

        return $chart;
    }

    private function getMessageChart(): Chart
    {
        $chart = $this->chartBuilder->createChart(Chart::TYPE_LINE);
        $chart->setData([
            'labels' => [],
            'datasets' => [
                [
                    'label' => 'All',
                    'data' => [],
                    'fill' => true,
                    'borderWidth' => 1,
                    'borderColor' => '#2185d0',
                ],
                [
                    'label' => 'Error',
                    'data' => [],
                    'fill' => true,
                    'borderWidth' => 1,
                    'borderColor' => '#767676',
                ],
            ],
        ]);

        $chart->setOptions(static::OPTIONS);

        return $chart;
    }

    private function getCount(string $entity): int
    {
        return $this->entityManager->getRepository($entity)
            ->createQueryBuilder('a')
            ->select('count(a.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
