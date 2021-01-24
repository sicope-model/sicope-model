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

namespace App\Widgets;

use Doctrine\ORM\EntityManagerInterface;
use Pd\WidgetBundle\Builder\Item;
use Pd\WidgetBundle\Event\WidgetEvent;
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
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
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
                    ->setName('widget_model_info.name')
                    ->setDescription('widget_model_info.description')
                    ->setTemplate('Admin/Testing/Widget/modelInfo.html.twig')
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
                    ->setName('widget_task_info.name')
                    ->setDescription('widget_task_info.description')
                    ->setTemplate('Admin/Testing/Widget/taskInfo.html.twig')
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
                    ->setName('widget_bug_info.name')
                    ->setDescription('widget_bug_info.description')
                    ->setTemplate('Admin/Testing/Widget/bugInfo.html.twig')
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
            );
    }
}
