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
 * Settings Menus.
 *
 * @author Ramazan APAYDIN <apaydin541@gmail.com>
 */
class NavModel extends Menu
{
    public function createMenu(array $options = []): ItemInterface
    {
        // Create Root Item
        $menu = $this->createRoot('model_toolbar')->setChildAttr([
            'sidebar' => 'testing.model',
        ]);

        // Create Menu Items
        $menu->addChild('list')
            ->setLabel('testing.model_list_title')
            ->setRoute('testing.model_list')
            ->setRoles(['ROLE_MODEL_LIST'])
            // Account
            ->addChildParent('build')
            ->setLabel('testing.build')
            ->setRoute('testing.model_build')
            ->setRoles(['ROLE_MODEL_BUILD']);

        return $menu;
    }
}
