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

namespace App\Menu;

use Pd\MenuBundle\Builder\ItemInterface;
use Pd\MenuBundle\Builder\Menu;

/**
 * Tasks Action.
 *
 * @author Tien Xuan Vo <tien.xuan.vo@gmail.com>
 */
class NavTask extends Menu
{
    public function createMenu(array $options = []): ItemInterface
    {
        // Create Root Menu
        $menu = $this->createRoot('testing_task_action', false);

        // Add Menu Items
        $menu
            ->addChild('testing_task_delete', 1)
            ->setLabel('delete')
            ->setRoute('testing.task_delete', ['task' => $options['task']->getId()])
            ->setRoles(['ROLE_TASK_DELETE'])
            ->setExtra('label_icon', 'delete')
            ->setLinkAttr([
                'class' => 'text-danger',
                'data-tooltip' => '',
                'title' => 'delete',
                'data-modal' => 'confirm',
            ])
            ->setLabelAttr(['class' => 'hidden'])

            ->addChildParent('testing_task_edit', 1)
            ->setLabel('edit')
            ->setRoute('testing.task_edit', ['task' => $options['task']->getId()])
            ->setRoles(['ROLE_TASK_EDIT'])
            ->setExtra('label_icon', 'mode_edit')
            ->setLinkAttr([
                'data-tooltip' => '',
                'title' => 'edit',
            ])
            ->setLabelAttr(['class' => 'hidden']);

        if (!$options['task']->isRunning()) {
            $menu
                ->addChild('testing_task_run', 1)
                ->setLabel('run')
                ->setRoute(
                    'testing.task_run',
                    ['task' => $options['task']->getId()]
                )
                ->setRoles(['ROLE_TASK_RUN'])
                ->setExtra('label_icon', 'play_arrow')
                ->setLinkAttr([
                    'data-tooltip' => '',
                    'title' => 'run',
                ])
                ->setLabelAttr(['class' => 'hidden']);
        }

        return $menu;
    }
}
