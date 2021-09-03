<?php

namespace App\Controller;

use App\Entity\Config;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Tienvx\Bundle\MbtBundle\Entity\Bug;
use Tienvx\Bundle\MbtBundle\Entity\Model;
use Tienvx\Bundle\MbtBundle\Entity\Task;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

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
        private EntityManagerInterface $entityManager
    ) {

    }

    #[Route('/', name: 'app_dashboard')]
    public function index(): Response
    {
        return $this->render('dashboard.html.twig', [
            'taskBugChart' => $this->getTaskBugChart(),
            'modelChart' => $this->getModelChart(),
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('SICOPE Model')
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
        yield MenuItem::linkToCrud('Config', 'fa fa-cogs', Config::class)
            ->setPermission('ROLE_ADMIN')
            ->setAction(Action::EDIT);
    }

    private function getTaskBugChart(): Chart
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
        $taskData = array_column($taskData, 'count', 'day');
        $bugData = array_column($bugData, 'count', 'day');

        // Optimize Data
        $labels = $tasks = $bugs = [];
        for ($i = static::COLUMNS - 1; $i >= 0; --$i) {
            $day = explode('/', date('j/m', strtotime("-{$i} day")));
            $labels[] = $day[0] . '/' . $day[1];
            $tasks[] = $taskData[$day[0]] ?? 0;
            $bugs[] = $bugData[$day[0]] ?? 0;
        }

        $chart = $this->chartBuilder->createChart(Chart::TYPE_LINE);
        $chart->setData([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Tasks',
                    'backgroundColor' => 'rgb(255, 99, 132, 0.2)',
                    'borderColor' => 'rgb(255, 99, 132, 0.8)',
                    'fill' => true,
                    'borderWidth' => 1,
                    'data' => $tasks,
                ],
                [
                    'label' => 'Bugs',
                    'backgroundColor' => 'rgba(48, 164, 255, 0.2)',
                    'borderColor' => 'rgba(48, 164, 255, 0.8)',
                    'fill' => true,
                    'borderWidth' => 1,
                    'data' => $bugs,
                ],
            ],
        ]);

        $chart->setOptions(static::OPTIONS);

        return $chart;
    }

    private function getModelChart(): Chart
    {
        $date = new \DateTime(static::TIME);

        // Load Records
        $modelData = $this->entityManager->getRepository(Task::class)
            ->createQueryBuilder('t')
            ->select('count(t.id) as count, DAY(t.createdAt) as day')
            ->groupBy('day')
            ->where('t.createdAt >= :date')
            ->setParameter('date', $date)
            ->getQuery()->getArrayResult();
        $modelData = array_column($modelData, 'count', 'day');

        // Optimize Data
        $labels = $models = [];
        for ($i = static::COLUMNS - 1; $i >= 0; --$i) {
            $day = explode('/', date('j/m', strtotime("-{$i} day")));
            $labels[] = $day[0] . '/' . $day[1];
            $models[] = $modelData[$day[0]] ?? 0;
        }

        $chart = $this->chartBuilder->createChart(Chart::TYPE_BAR);
        $chart->setData([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Models',
                    'backgroundColor' => 'rgba(76, 175, 80, 0.5)',
                    'borderColor' => '#6da252',
                    'borderWidth' => 1,
                    'data' => $models,
                ],
            ],
        ]);

        $chart->setOptions(static::OPTIONS);

        return $chart;
    }
}
