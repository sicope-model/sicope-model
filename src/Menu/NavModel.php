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
 * Models Action.
 *
 * @author Tien Xuan Vo <tien.xuan.vo@gmail.com>
 */
class NavModel extends Menu
{
    public function createMenu(array $options = []): ItemInterface
    {
        // Create Root Menu
        $menu = $this->createRoot('testing_model_action', false);

        // Add Menu Items
        $menu
            ->addChild('testing_model_delete', 1)
            ->setLabel('delete')
            ->setRoute('testing.model_delete', ['model' => $options['model']->getId()])
            ->setRoles(['ROLE_MODEL_DELETE'])
            ->setExtra('label_icon', 'delete')
            ->setLinkAttr([
                'class' => 'text-danger',
                'data-tooltip' => '',
                'title' => 'delete',
                'data-modal' => 'confirm',
            ])
            ->setLabelAttr(['class' => 'hidden'])

            ->addChildParent('testing_model_edit', 1)
            ->setLabel('edit')
            ->setRoute('testing.model_edit', ['model' => $options['model']->getId()])
            ->setRoles(['ROLE_MODEL_EDIT'])
            ->setExtra('label_icon', 'mode_edit')
            ->setLinkAttr([
                'data-tooltip' => '',
                'title' => 'edit',
            ])
            ->setLabelAttr(['class' => 'hidden'])

            ->addChildParent('testing_model_export', 1)
            ->setLabel('export')
            ->setRoute('testing.model_export', ['model' => $options['model']->getId()])
            ->setRoles(['ROLE_MODEL_EXPORT'])
            ->setExtra('label_icon', 'download')
            ->setLinkAttr([
                'data-tooltip' => '',
                'title' => 'export',
            ])
            ->setLabelAttr(['class' => 'hidden']);

        return $menu;
    }
}
