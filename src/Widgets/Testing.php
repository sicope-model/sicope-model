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

namespace App\Widgets;

use Doctrine\ORM\EntityManagerInterface;
use Pd\WidgetBundle\Builder\Item;
use Pd\WidgetBundle\Event\WidgetEvent;
use Symfony\Component\HttpFoundation\Request;
use Tienvx\Bundle\MbtBundle\Entity\Bug;
use Tienvx\Bundle\MbtBundle\Entity\Model;
use Tienvx\Bundle\MbtBundle\Entity\Task;

/**
 * Testing Widgets.
 *
 * @author Tien Xuan Vo <tien.xuan.vo@gmail.com>
 */
class Testing
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    /**
     * Build Widgets.
     */
    public function builder(WidgetEvent $event): void
    {
        // Get Widget Container
        $widgets = $event->getWidgetContainer();

        // Add Widgets
        $widgets
            ->addWidget(
                (new Item('model_info'))
                    ->setGroup('admin')
                    ->setName('testing.widget.model_info.name')
                    ->setDescription('testing.widget.model_info.description')
                    ->setTemplate('testing/widget/modelInfo.html.twig')
                    ->setRole(['ROLE_WIDGET_MODELINFO'])
                    ->setData(function ($config) {
                        $modelCount = $this->entityManager->getRepository(Model::class)
                            ->createQueryBuilder('m')
                            ->select('count(m.id)')
                            ->getQuery()
                            ->getSingleScalarResult();

                        return ['result' => $modelCount];
                    })
                    ->setOrder(5)
            )
            ->addWidget(
                (new Item('task_info'))
                    ->setGroup('admin')
                    ->setName('testing.widget.task_info.name')
                    ->setDescription('testing.widget.task_info.description')
                    ->setTemplate('testing/widget/taskInfo.html.twig')
                    ->setRole(['ROLE_WIDGET_TASKINFO'])
                    ->setData(function ($config) {
                        $taskCount = $this->entityManager->getRepository(Task::class)
                            ->createQueryBuilder('t')
                            ->select('count(t.id)')
                            ->getQuery()
                            ->getSingleScalarResult();

                        return ['result' => $taskCount];
                    })
                    ->setOrder(5)
            )
            ->addWidget(
                (new Item('bug_info'))
                    ->setGroup('admin')
                    ->setName('testing.widget.bug_info.name')
                    ->setDescription('testing.widget.bug_info.description')
                    ->setTemplate('testing/widget/bugInfo.html.twig')
                    ->setRole(['ROLE_WIDGET_BUGINFO'])
                    ->setData(function ($config) {
                        $bugCount = $this->entityManager->getRepository(Bug::class)
                            ->createQueryBuilder('b')
                            ->select('count(b.id)')
                            ->getQuery()
                            ->getSingleScalarResult();

                        return ['result' => $bugCount];
                    })
                    ->setOrder(5)
            )
            ->addWidget(
                (new Item('testing_statistics'))
                    ->setGroup('admin')
                    ->setName('testing.widget.testing_statistics.name')
                    ->setDescription('testing.widget.testing_statistics.description')
                    ->setTemplate('testing/widget/testingStatistics.html.twig')
                    ->setRole(['ROLE_WIDGET_TESTINGSTATISTICS'])
                    ->setConfigProcess(static function (Request $request) {
                        if ($type = $request->get('type')) {
                            switch ($type) {
                                case '1week':
                                    return ['type' => '1week'];
                                case '1month':
                                    return ['type' => '1month'];
                                case '3month':
                                    return ['type' => '3month'];
                            }
                        }

                        return false;
                    })
                    ->setData(function ($config) {
                        // Create Chart Data
                        $chart = [
                            'column' => [],
                            'task' => [],
                            'bug' => [],
                        ];

                        // Set Default
                        if (!isset($config['type'])) {
                            $config['type'] = '1week';
                        }

                        // Create Statistics Data
                        if ('3month' === $config['type']) {
                            // Load Records
                            $taskData = $this->entityManager->getRepository(Task::class)
                                ->createQueryBuilder('t')
                                ->select('count(t.id) as count, MONTH(t.createdAt) as month')
                                ->groupBy('month')
                                ->where('t.createdAt >= :date')
                                ->setParameter('date', new \DateTime('-3 Month'))
                                ->getQuery()->getArrayResult();
                            $bugData = $this->entityManager->getRepository(Bug::class)
                                ->createQueryBuilder('b')
                                ->select('count(b.id) as count, MONTH(b.createdAt) as month')
                                ->groupBy('month')
                                ->where('b.createdAt >= :date')
                                ->setParameter('date', new \DateTime('-3 Month'))
                                ->getQuery()->getArrayResult();
                            $taskData = array_column($taskData, 'count', 'month');
                            $bugData = array_column($bugData, 'count', 'month');

                            // Optimize Data
                            for ($i = 0; $i < 3; ++$i) {
                                $month = explode('/', date('n/Y', strtotime("-{$i} month")));
                                $chart['column'][] = $month[0] . '/' . $month[1];
                                $chart['task'][] = $taskData[$month[0]] ?? 0;
                                $chart['bug'][] = $bugData[$month[0]] ?? 0;
                            }
                        } elseif (\in_array($config['type'], ['1month', '1week'], true) || !$config['type']) {
                            $time = '1month' === $config['type'] ? new \DateTime('-1 Month') : new \DateTime('-6 Day');
                            $column = '1month' === $config['type'] ? 30 : 7;

                            // Load Records
                            $taskData = $this->entityManager->getRepository(Task::class)
                                ->createQueryBuilder('t')
                                ->select('count(t.id) as count, DAY(t.createdAt) as day')
                                ->groupBy('day')
                                ->where('t.createdAt >= :date')
                                ->setParameter('date', $time)
                                ->getQuery()->getArrayResult();
                            $bugData = $this->entityManager->getRepository(Bug::class)
                                ->createQueryBuilder('b')
                                ->select('count(b.id) as count, DAY(b.createdAt) as day')
                                ->groupBy('day')
                                ->where('b.createdAt >= :date')
                                ->setParameter('date', $time)
                                ->getQuery()->getArrayResult();
                            $taskData = array_column($taskData, 'count', 'day');
                            $bugData = array_column($bugData, 'count', 'day');

                            // Optimize Data
                            for ($i = 0; $i < $column; ++$i) {
                                $day = explode('/', date('j/m', strtotime("-{$i} day")));
                                $chart['column'][] = $day[0] . '/' . $day[1];
                                $chart['task'][] = $taskData[$day[0]] ?? 0;
                                $chart['bug'][] = $bugData[$day[0]] ?? 0;
                            }
                        }

                        // JSON & Reverse Data
                        $chart['column'] = json_encode(array_reverse($chart['column']));
                        $chart['task'] = json_encode(array_reverse($chart['task']));
                        $chart['bug'] = json_encode(array_reverse($chart['bug']));

                        return $chart;
                    })
            );
    }
}
