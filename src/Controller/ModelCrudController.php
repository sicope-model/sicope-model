<?php

namespace App\Controller;

use App\Form\Model\RevisionType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Tienvx\Bundle\MbtBundle\Entity\Model;

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
        yield IdField::new('activeRevision', 'Revision')
            ->setFormType(RevisionType::class)
            ->setFormTypeOptions([
                'label' => false,
            ])
            ->addCssClass('field-collection')
            ->addJsFiles('bundles/easyadmin/form-type-collection.js')
            ->setDefaultColumns('col-md-8 col-xxl-7');
    }
}
