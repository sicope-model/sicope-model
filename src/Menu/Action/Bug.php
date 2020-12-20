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
            ->setLabelAttr(['class' => 'hidden']);

        return $menu;
    }
}
