<?php

/**
 * This file is part of the SICOPE Model package.
 *
 * @package     sicope-model
 * @license     LICENSE
 * @author      Tien Xuan Vo <tien.xuan.vo@gmail.com>
 * @link        https://github.com/sicope-model/sicope-model
 */

namespace App\Field\Configurator;

use App\Repository\UserRepository;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldConfiguratorInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FieldDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;

final class AuthorConfigurator implements FieldConfiguratorInterface
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    public function supports(FieldDto $field, EntityDto $entityDto): bool
    {
        return IdField::class === $field->getFieldFqcn() && 'author' === $field->getProperty();
    }

    public function configure(FieldDto $field, EntityDto $entityDto, AdminContext $context): void
    {
        $fieldValue = $field->getValue();
        $formattedValue = '';
        if (\is_int($fieldValue)) {
            $user = $this->userRepository->find($fieldValue);

            $formattedValue = $user ? $user->getFullName() : '';
        }

        $field->setFormattedValue($formattedValue);
    }

    public static function getDefaultPriority(): int
    {
        return -1;
    }
}
