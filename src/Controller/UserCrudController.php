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

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AvatarField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use ReflectionClass;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        $roles = [
            'ROLE_USER',
            'ROLE_ADMIN',
        ];
        yield IdField::new('id')->onlyOnDetail();
        yield AvatarField::new('email')->setIsGravatarEmail()->hideOnForm();
        yield TextField::new('fullName');
        yield TextField::new('username');
        yield EmailField::new('email');
        yield ChoiceField::new('roles')->setChoices(array_combine($roles, $roles))->allowMultipleChoices();
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions->setPermissions(array_fill_keys(
            (new ReflectionClass(Action::class))->getConstants(),
            'ROLE_ADMIN'
        ));
    }
}
