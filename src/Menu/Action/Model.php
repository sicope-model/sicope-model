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
 * Models Action.
 *
 * @author Tien Xuan Vo <tien.xuan.vo@gmail.com>
 */
class Model extends Menu
{
    public function createMenu(array $options = []): ItemInterface
    {
        // Create Root Menu
        $menu = $this->createRoot('account_action_menu', false);

        // Add Menu Items
        $menu
            ->addChild('admin_testing_model_delete', 1)
            ->setLabel('delete')
            ->setRoute('admin_model_delete', ['model' => $options['model']->getId()])
            ->setRoles(['ROLE_MODEL_DELETE'])
            ->setExtra('label_icon', 'delete')
            ->setLinkAttr([
                'class' => 'text-danger',
                'data-tooltip' => '',
                'title' => 'delete',
                'data-modal' => 'confirm',
            ])
            ->setLabelAttr(['class' => 'hidden'])

            ->addChildParent('admin_testing_model_edit', 1)
            ->setLabel('edit')
            ->setRoute('admin_model_edit', ['model' => $options['model']->getId()])
            ->setRoles(['ROLE_MODEL_EDIT'])
            ->setExtra('label_icon', 'mode_edit')
            ->setLinkAttr([
                'data-tooltip' => '',
                'title' => 'edit',
            ])
            ->setLabelAttr(['class' => 'hidden'])

            ->addChildParent('admin_testing_model_export', 1)
            ->setLabel('export')
            ->setRoute('admin_model_export', ['model' => $options['model']->getId()])
            ->setRoles(['ROLE_MODEL_EXPORT'])
            ->setExtra('label_icon', 'get_app')
            ->setLinkAttr([
                'data-tooltip' => '',
                'title' => 'export',
            ])
            ->setLabelAttr(['class' => 'hidden']);

        return $menu;
    }
}
