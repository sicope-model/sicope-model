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

namespace App\Menu\Action;

use Pd\MenuBundle\Builder\ItemInterface;
use Pd\MenuBundle\Builder\Menu;

/**
 * Bugs Action.
 *
 * @author Tien Xuan Vo <tien.xuan.vo@gmail.com>
 */
class Bug extends Menu
{
    public function createMenu(array $options = []): ItemInterface
    {
        // Create Root Menu
        $menu = $this->createRoot('account_action_menu', false);

        // Add Menu Items
        $menu
            ->addChild('admin_testing_bug_delete', 1)
            ->setLabel('delete')
            ->setRoute('admin_bug_delete', ['bug' => $options['bug']->getId()])
            ->setRoles(['ROLE_BUG_DELETE'])
            ->setExtra('label_icon', 'delete')
            ->setLinkAttr([
                'class' => 'text-danger',
                'data-tooltip' => '',
                'title' => 'delete',
                'data-modal' => 'confirm',
            ])
            ->setLabelAttr(['class' => 'hidden'])

            ->addChildParent('admin_testing_bug_edit', 1)
            ->setLabel('edit')
            ->setRoute('admin_bug_edit', ['bug' => $options['bug']->getId()])
            ->setRoles(['ROLE_BUG_EDIT'])
            ->setExtra('label_icon', 'mode_edit')
            ->setLinkAttr([
                'data-tooltip' => '',
                'title' => 'edit',
            ])
            ->setLabelAttr(['class' => 'hidden'])

            ->addChildParent('admin_testing_bug_update_status', 1)
            ->setLabel($options['bug']->isClosed() ? 'open' : 'close')
            ->setRoute(
                $options['bug']->isClosed() ? 'admin_bug_open' : 'admin_bug_close',
                ['bug' => $options['bug']->getId()]
            )
            ->setRoles($options['bug']->isClosed() ? ['ROLE_BUG_OPEN'] : ['ROLE_BUG_CLOSE'])
            ->setExtra('label_icon', $options['bug']->isClosed() ? 'drafts' : 'mail')
            ->setLinkAttr([
                'data-tooltip' => '',
                'title' => $options['bug']->isClosed() ? 'open' : 'close',
            ])
            ->setLabelAttr(['class' => 'hidden'])

            ->addChildParent('admin_testing_bug_export', 1)
            ->setLabel('export')
            ->setRoute('admin_bug_export', ['bug' => $options['bug']->getId()])
            ->setRoles(['ROLE_BUG_EXPORT'])
            ->setExtra('label_icon', 'download')
            ->setLinkAttr([
                'data-tooltip' => '',
                'title' => 'export',
            ])
            ->setLabelAttr(['class' => 'hidden']);

        if (!$options['bug']->isReducing()) {
            $menu
                ->addChild('admin_testing_bug_reduce', 1)
                ->setLabel('reduce')
                ->setRoute(
                    'admin_bug_reduce',
                    ['bug' => $options['bug']->getId()]
                )
                ->setRoles(['ROLE_BUG_REDUCE'])
                ->setExtra('label_icon', 'compress')
                ->setLinkAttr([
                    'data-tooltip' => '',
                    'title' => 'reduce',
                ])
                ->setLabelAttr(['class' => 'hidden']);
        }

        return $menu;
    }
}
