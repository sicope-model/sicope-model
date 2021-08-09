<?php

namespace App\Controller;

use App\Field\ModelRevisionField;
use App\Form\Model\PlaceType;
use App\Form\Model\TransitionType;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
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
        yield ArrayField::new('tags');
        yield CollectionField::new('activeRevision.places', 'Places')
            ->allowAdd()
            ->allowDelete()
            ->setEntryIsComplex(true)
            ->setEntryType(PlaceType::class)
            ->setFormTypeOptions([
                'by_reference' => false,
            ]);
        yield CollectionField::new('activeRevision.transitions', 'Transitions')
            ->allowAdd()
            ->allowDelete()
            ->setEntryIsComplex(true)
            ->setEntryType(TransitionType::class)
            ->setFormTypeOptions([
                'by_reference' => false,
            ]);
        //yield ModelRevisionField::new('activeRevision', 'Revision');
    }
}
