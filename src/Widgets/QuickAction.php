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

use Pd\WidgetBundle\Builder\Item;
use Pd\WidgetBundle\Event\WidgetEvent;
use Symfony\Component\HttpFoundation\Request;

/**
 * Quick Action Widget.
 *
 * @author Ramazan APAYDIN <apaydin541@gmail.com>
 */
class QuickAction
{
    /**
     * Build Widgets.
     */
    public function builder(WidgetEvent $event): void
    {
        // Get Widget Container
        $widgets = $event->getWidgetContainer();

        // Action Button
        $items = [
            'action_account' => [
                'name' => 'nav_account',
                'description' => 'admin_account_desc',
                'route' => 'admin_account_list',
                'icons' => 'person',
                'linkClass' => 'btn btn-primary',
            ],
            'action_group' => [
                'name' => 'nav_group',
                'description' => 'account_group_list_title',
                'route' => 'admin_group_list',
                'icons' => 'group',
                'linkClass' => 'btn btn-primary',
            ],
            'action_settings' => [
                'name' => 'settings_general',
                'description' => 'settings_general_desc',
                'route' => 'admin_settings_general',
                'icons' => 'settings',
                'linkClass' => 'btn btn-secondary',
            ],
            'mail_manager' => [
                'name' => 'mail_manager_list',
                'description' => 'mail_manager_list_desc',
                'route' => 'mail_template',
                'icons' => 'email',
                'linkClass' => 'btn btn-secondary',
            ],
            'mail_manager_logs' => [
                'name' => 'mail_manager_logger',
                'description' => 'mail_manager_logger_desc',
                'route' => 'mail_log',
                'icons' => 'send',
                'linkClass' => 'btn btn-secondary',
            ],
            'testing_model' => [
                'name' => 'nav_model',
                'description' => 'testing_model_list_desc',
                'route' => 'admin_model_list',
                'icons' => 'device_hub',
                'linkClass' => 'btn btn-secondary',
            ],
            'testing_task' => [
                'name' => 'nav_task',
                'description' => 'testing_task_list_desc',
                'route' => 'admin_task_list',
                'icons' => 'assignment',
                'linkClass' => 'btn btn-secondary',
            ],
            'testing_bug' => [
                'name' => 'nav_bug',
                'description' => 'testing_bug_list_desc',
                'route' => 'admin_bug_list',
                'icons' => 'bug_report',
                'linkClass' => 'btn btn-secondary',
            ],
        ];

        // Add Widgets
        $widgets
            ->addWidget(
                (new Item('quick_action'))
                    ->setGroup('admin')
                    ->setName('widget_quick_action.name')
                    ->setDescription('widget_quick_action.description')
                    ->setTemplate('Admin/Widget/quickAction.html.twig')
                    ->setRole(['ROLE_WIDGET_QUICKACTION'])
                    ->setConfigProcess(static function (Request $request) use ($items) {
                        if (($id = $request->get('id')) && isset($items[$id])) {
                            return [$id => $items[$id]];
                        }

                        return false;
                    })
                    ->setData(static function ($config) use ($items) {
                        return ['items' => $items];
                    })
                    ->setOrder(0)
            );
    }
}
