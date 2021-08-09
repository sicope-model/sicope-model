<?php

namespace App\Field;

use App\Form\Model\RevisionType;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\FieldTrait;

final class ModelRevisionField implements FieldInterface
{
    use FieldTrait;

    /**
     * @param string|false|null $label
     */
    public static function new(string $propertyName, $label = null): self
    {
        return (new self())
            ->setProperty($propertyName)
            ->setLabel($label)
            ->setTemplateName('field/revision')
            ->setFormType(RevisionType::class)
            ->addCssClass('field-revision')
            ->addWebpackEncoreEntries('app');
    }
}
