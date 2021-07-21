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
 * Main Navigation.
 *
 * @author Ramazan APAYDIN <apaydin541@gmail.com>
 */
class Sidebar extends Menu
{
    public function createMenu(array $options = []): ItemInterface
    {
        // Create ROOT Menu
        $menu = $this->createRoot('main_menu', true);
        $menu->addChild('nav_dashboard', 1)
            ->setLabel('dashboard.title')
            ->setRoute('admin.dashboard')
            ->setRoles(['ROLE_DASHBOARD_PANEL'])
            ->setExtra('label_icon', 's fa-chart-pie');

        /*
         * Testing Menus
         */
        $menu
            ->addChild('testing', 10)
            ->setLabel('testing.testing')
            ->setRoute('testing.task_list')
            ->setRoles(['ROLE_TASK_LIST'])
            ->setExtra('label_icon', 'important_devices')
                // Task List
                ->addChild('testing.task', 10)
                ->setLabel('testing.task')
                ->setRoute('testing.task_list')
                ->setRoles(['ROLE_TASK_LIST'])
                ->setExtra('label_icon', 'assignment')
                // Model List
                ->addChildParent('testing.model', 20)
                ->setLabel('testing.model')
                ->setRoute('testing.model_list')
                ->setRoles(['ROLE_MODEL_LIST'])
                ->setExtra('label_icon', 'device_hub')
                // Bug List
                ->addChildParent('testing.bug', 30)
                ->setLabel('testing.bug')
                ->setRoute('testing.bug_list')
                ->setRoles(['ROLE_BUG_LIST'])
                ->setExtra('label_icon', 'bug_report');

        /*
         * Account Menus
         */
        $menu
            ->addChild('account_group', 20)
            ->setLabel('accounts.title')
            ->setRoute('admin.account_list')
            ->setRoles(['ROLE_ACCOUNT_LIST'])
            ->setExtra('label_icon', 's fa-user-shield')
                // Account List
                ->addChild('accounts', 10)
                ->setLabel('accounts.account.title')
                ->setRoute('admin.account_list')
                ->setRoles(['ROLE_ACCOUNT_LIST'])
                // Group List
                ->addChildParent('groups', 20)
                ->setLabel('accounts.group.title')
                ->setRoute('admin.group_list')
                ->setRoles(['ROLE_GROUP_LIST']);

        /*
         * Settings Menus
         */
        $menu
            ->addChild('config', 50)
            ->setLabel('config.title')
            ->setRoute('admin.config_general')
            ->setExtra('label_icon', 's fa-cogs')
            ->setRoles(['ROLE_CONFIG_GENERAL'])
                // Admin Settings
                ->addChild('config.system', 10)
                ->setLabel('config.system')
                ->setRoute('admin.config_general')
                ->setRoles(['ROLE_CONFIG_GENERAL'])
                // Activity Log HTTP
                ->addChildParent('activity.http', 20)
                ->setLabel('activity.log.title')
                ->setRoute('admin.activity_log.http')
                ->setRoles(['ROLE_ACTIVITY_HTTP'])
                // Activity Log Mail
                ->addChildParent('activity.mail', 30)
                ->setLabel('activity.mail.title')
                ->setRoute('admin.activity_log.mail')
                ->setRoles(['ROLE_ACTIVITY_MAIL']);

        return $menu;
    }
}
