<?php

/**
 * This file is part of the SICOPE Model package.
 *
 * @package     sicope-model
 * @license     LICENSE
 * @author      Tien Xuan Vo <tien.xuan.vo@gmail.com>
 * @link        https://github.com/sicope-model/sicope-model
 */

namespace App\Controller;

use App\Form\Model\RevisionType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\HiddenField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Tienvx\Bundle\MbtBundle\Entity\Model;
use Tienvx\Bundle\MbtBundle\Model\Model\RevisionInterface;

class ModelCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Model::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->onlyOnDetail();
        yield TextField::new('label');
        yield TextField::new('tags')
            ->setFormTypeOptions([
                'attr' => [
                    'data-controller' => 'tags',
                ],
            ])
            ->addWebpackEncoreEntries('app');
        yield HiddenField::new('activeRevision', 'Revision')
            ->formatValue(function (RevisionInterface $value) {
                return sprintf('id %d, %d place(s), %d transition(s)', $value->getId(), \count($value->getPlaces()), \count($value->getTransitions()));
            })
            ->setFormType(RevisionType::class)
            ->setFormTypeOptions([
                'label' => false,
                'attr' => [
                    'data-controller' => 'places',
                    'data-action' => 'place-label:added->places#addOption place-label:removed@window->places#removeOption place-label:updated->places#updateOption places-select:added->places#setOptions',
                ],
            ])
            ->addCssClass('field-collection')
            ->addJsFiles('bundles/easyadmin/form-type-collection.js')
            ->setDefaultColumns('col-md-8 col-xxl-7');
    }
}
