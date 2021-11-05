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

use App\Service\DebugHelper;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;
use Tienvx\Bundle\MbtBundle\Entity\Bug;
use Tienvx\Bundle\MbtBundle\Model\BugInterface;

class BugCrudController extends AbstractCrudController
{
    public function __construct(private DebugHelper $debugHelper)
    {
    }

    #[Route('/bug-video/{bug}', name: 'app_bug_video')]
    public function bugVideo(Bug $bug): StreamedResponse
    {
        return $this->debugHelper->streamVideo($bug);
    }

    public static function getEntityFqcn(): string
    {
        return Bug::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('title');
        yield TextareaField::new('message')->setDisabled(true);
        yield AssociationField::new('task')->hideOnForm();
        yield DateTimeField::new('createdAt', 'Created At')->hideOnForm();
        yield ArrayField::new('steps', 'Steps')->setTemplatePath('field/steps.html.twig')->onlyOnDetail();

        yield FormField::addPanel('Debug')->onlyOnDetail();
        yield TextEditorField::new('id', 'Log')
            ->onlyOnDetail()
            ->formatValue(fn (int $id, BugInterface $bug) => $this->debugHelper->getLog($bug));
        yield UrlField::new('id', 'Video')
            ->onlyOnDetail()
            ->setTemplatePath('field/video.html.twig')
            ->formatValue(fn (int $id) => $this->generateUrl('app_bug_video', ['bug' => $id]));
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable(Action::NEW)
            ->update(Crud::PAGE_INDEX, Action::EDIT, fn (Action $detail) => $detail->setIcon('fas fa-edit'))
            ->update(Crud::PAGE_INDEX, Action::DELETE, fn (Action $detail) => $detail->setIcon('fas fa-trash'))
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->update(Crud::PAGE_INDEX, Action::DETAIL, fn (Action $detail) => $detail->setIcon('fas fa-info'));
    }
}
