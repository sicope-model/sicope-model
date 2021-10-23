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
use App\Service\BrowserFormatter;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Tienvx\Bundle\MbtBundle\Entity\Model;
use Tienvx\Bundle\MbtBundle\Entity\Model\Revision;
use Tienvx\Bundle\MbtBundle\Entity\Task;
use Tienvx\Bundle\MbtBundle\Message\RunTaskMessage;
use Tienvx\Bundle\MbtBundle\Model\TaskInterface;

class TaskCrudController extends AbstractCrudController
{
    public function __construct(
        private HttpClientInterface $client,
        private string $statusUri,
        private BrowserFormatter $formatter
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return Task::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->onlyOnDetail();
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
        ;
        yield ChoiceField::new('browser', 'Browser')
            ->setChoices($this->getBrowserChoices())
            ->setFormType(BrowserType::class)
            ->setRequired(true)
        ;
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
            ->update(Crud::PAGE_INDEX, Action::DELETE, fn (Action $detail) => $detail->setIcon('fas fa-trash'))
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

    private function getBrowserChoices(): array
    {
        $response = $this->client->request(
            'GET',
            rtrim($this->statusUri, '/') . '/status'
        );
        $choices = [];

        foreach ($response->toArray()['browsers'] ?? [] as $name => $versions) {
            if (1 === \count($versions)) {
                $version = key($versions);
                $choices[$this->formatter->format($name, $version)] = sprintf('%s:%s', $name, $version);
            } else {
                foreach ($versions as $version => $session) {
                    $choices[$name][$this->formatter->format($name, $version)] = sprintf('%s:%s', $name, $version);
                }
            }
        }

        return $choices;
    }
}
