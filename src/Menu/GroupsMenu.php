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

namespace App\Menu;

use Pd\MenuBundle\Builder\ItemInterface;
use Pd\MenuBundle\Builder\Menu;

/**
 * Groups Menus.
 *
 * @author Ramazan APAYDIN <apaydin541@gmail.com>
 */
class GroupsMenu extends Menu
{
    public function createMenu(array $options = []): ItemInterface
    {
        // Create Root Menu
        $menu = $this->createRoot('groups_menu')->setChildAttr([
            'class' => 'nav nav-pills',
            'data-parent' => 'admin_group_list',
        ]);

        // Add Menu Items
        $menu
            ->addChild('nav_group_edit', 1)
            ->setLabel('nav_group_edit')
            ->setRoute('admin_group_edit', ['group' => $options['item'] ?? 0])
            ->setRoles(['ROLE_GROUP_EDIT'])

            ->addChildParent('nav_group_roles', 5)
            ->setLabel('nav_group_roles')
            ->setRoute('admin_group_roles', ['group' => $options['item'] ?? 0])
            ->setRoles(['ROLE_GROUP_ROLES']);

        return $menu;
    }
}
