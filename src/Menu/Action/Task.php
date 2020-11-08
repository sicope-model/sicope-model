<?php

namespace App\Menu\Action;

use Pd\MenuBundle\Builder\ItemInterface;
use Pd\MenuBundle\Builder\Menu;

/**
 * Tasks Action.
 *
 * @author Tien Xuan Vo <tien.xuan.vo@gmail.com>
 */
class Task extends Menu
{
    public function createMenu(array $options = []): ItemInterface
    {
        // Create Root Menu
        $menu = $this->createRoot('account_action_menu', false);

        // Add Menu Items
        $menu
            ->addChild('admin_account_group_delete', 1)
            ->setLabel('delete')
            ->setRoute('admin_group_delete', ['group' => $options['group']->getId()])
            ->setRoles(['ROLE_GROUP_DELETE'])
            ->setExtra('label_icon', 'delete')
            ->setLinkAttr([
                'class' => 'text-danger',
                'data-tooltip' => '',
                'title' => 'delete',
                'data-modal' => 'confirm',
            ])
            ->setLabelAttr(['class' => 'hidden'])

            ->addChildParent('admin_account_group_roles', 1)
            ->setLabel('edit_roles')
            ->setRoute('admin_group_roles', ['group' => $options['group']->getId()])
            ->setRoles(['ROLE_ACCOUNT_ACTIVATE'])
            ->setExtra('label_icon', 'lock')
            ->setLinkAttr([
                'data-tooltip' => '',
                'title' => 'edit_roles',
            ])
            ->setLabelAttr(['class' => 'hidden'])

            ->addChildParent('admin_account_group_edit', 1)
            ->setLabel('edit')
            ->setRoute('admin_group_edit', ['group' => $options['group']->getId()])
            ->setRoles(['ROLE_ACCOUNT_FREEZE'])
            ->setExtra('label_icon', 'mode_edit')
            ->setLinkAttr([
                'data-tooltip' => '',
                'title' => 'edit',
            ])
            ->setLabelAttr(['class' => 'hidden']);

        return $menu;
    }
}
