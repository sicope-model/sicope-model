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

use App\Form\Task\BrowserType;
use App\Service\DebugHelper;
use App\Service\SessionHelper;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tienvx\Bundle\MbtBundle\Entity\Model;
use Tienvx\Bundle\MbtBundle\Entity\Model\Revision;
use Tienvx\Bundle\MbtBundle\Entity\Task;
use Tienvx\Bundle\MbtBundle\Message\RunTaskMessage;
use Tienvx\Bundle\MbtBundle\Model\TaskInterface;

class TaskCrudController extends AbstractCrudController
{
    public function __construct(
        private SessionHelper $sessionHelper,
        private TranslatorInterface $translator,
        private DebugHelper $debugHelper
    ) {
    }

    #[Route('/task-video/{task}', name: 'app_task_video')]
    public function taskVideo(Task $task): StreamedResponse
    {
        return $this->debugHelper->streamVideo($task);
    }

    public static function getEntityFqcn(): string
    {
        return Task::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('title');
        yield BooleanField::new('running')->setDisabled(true);
        yield IdField::new('author')->hideOnForm();
        yield AssociationField::new('modelRevision', 'Model')
            ->setQueryBuilder(function (QueryBuilder $queryBuilder) {
                return $queryBuilder
                    ->select('r')
                    ->from(Revision::class, 'r')
                    ->where($queryBuilder->expr()->in(
                        'r.id',
                        $queryBuilder
                            ->getEntityManager()
                            ->createQueryBuilder()
                            ->select('IDENTITY(m.activeRevision)')
                            ->from(Model::class, 'm')
                            ->getDQL()
                    ));
            })
            ->setRequired(true)
            ->setTemplatePath('field/model.html.twig')
        ;
        $browserChoices = $this->sessionHelper->getBrowserChoices();
        $browserCount = $this->getBrowserCount($browserChoices);
        yield ChoiceField::new('browser', 'Browser')
            ->setChoices($browserChoices)
            ->setFormTypeOption('group_by', function ($choice) use ($browserCount): ?string {
                [$browser] = explode(':', $choice);

                return $browserCount[$browser] > 1 ? $this->translator->trans($browser) : null;
            })
            ->setFormType(BrowserType::class)
            ->setRequired(true)
        ;
        yield IntegerField::new('bugs', 'Number of bugs')
            ->formatValue(function (Collection $bugs) {
                return $bugs->count();
            })
            ->hideOnForm();
        yield DateTimeField::new('createdAt', 'Created At')->hideOnForm();

        yield FormField::addPanel('Debug')->onlyOnDetail();
        yield BooleanField::new('debug', 'Debug')->onlyWhenCreating();
        yield TextEditorField::new('id', 'Log')
            ->onlyOnDetail()
            ->formatValue(function (int $id, TaskInterface $task) {
                if (!$task->isDebug()) {
                    return null;
                }

                return $this->debugHelper->getLog($task);
            });
        yield UrlField::new('id', 'Video')
            ->onlyOnDetail()
            ->setTemplatePath('field/video.html.twig')
            ->formatValue(function (int $id, TaskInterface $task) {
                if (!$task->isDebug()) {
                    return null;
                }

                return $this->generateUrl('app_task_video', ['task' => $id]);
            });
    }

    public function configureActions(Actions $actions): Actions
    {
        $runTask = Action::new('runTask', 'Run Task', 'fa fa-play')
            ->displayIf(static function (TaskInterface $task) {
                return !$task->isRunning();
            })
            ->linkToCrudAction('runTask');

        return $actions
            ->update(Crud::PAGE_INDEX, Action::EDIT, fn (Action $detail) => $detail->setIcon('fas fa-edit'))
            ->update(
                Crud::PAGE_INDEX,
                Action::DELETE,
                fn (Action $detail) => $detail
                    ->setIcon('fas fa-trash')
                    ->addCssClass('action-delete')
                    ->displayIf(fn (TaskInterface $task) => 0 === $task->getBugs()->count())
            )
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->update(Crud::PAGE_INDEX, Action::DETAIL, fn (Action $detail) => $detail->setIcon('fas fa-info'))
            ->add(Crud::PAGE_DETAIL, $runTask)
            ->add(Crud::PAGE_INDEX, $runTask);
    }

    public function runTask(AdminContext $context, MessageBusInterface $messageBus): RedirectResponse
    {
        $task = $context->getEntity()->getInstance();

        if ($task->isRunning()) {
            $this->addFlash('error', 'Task is already running');
        } else {
            $messageBus->dispatch(new RunTaskMessage($task->getId()));
            $this->addFlash('success', 'Task is scheduled');
        }

        return $this->redirect($this->get(AdminUrlGenerator::class)->setAction(Action::INDEX)->generateUrl());
    }

    protected function getBrowserCount(array $browserChoices): array
    {
        $browserCount = [];
        foreach ($browserChoices as $choice) {
            [$browser] = explode(':', $choice);
            $browserCount[$browser] = isset($browserCount[$browser]) ? ++$browserCount[$browser] : 1;
        }

        return $browserCount;
    }
}
