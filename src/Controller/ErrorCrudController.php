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

use App\Entity\Error;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Option\EA;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use ReflectionClass;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Messenger\Stamp\ErrorDetailsStamp;
use Symfony\Component\Messenger\Transport\Serialization\PhpSerializer;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;

#[IsGranted('ROLE_ADMIN')]
class ErrorCrudController extends AbstractCrudController
{
    public function __construct(private SerializerInterface $serializer)
    {
        $this->serializer = $this->serializer ?? new PhpSerializer();
    }

    public static function getEntityFqcn(): string
    {
        return Error::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('body')
            ->hideOnForm()
            ->formatValue(function (string $body, Error $error): string {
                $envelope = $this->serializer->decode([
                    'body' => $error->getBody(),
                    'headers' => $error->getHeaders(),
                ]);
                $stamp = $envelope->last(ErrorDetailsStamp::class);

                return $stamp ? $stamp->getExceptionMessage() : '';
            });
        yield DateTimeField::new('createdAt')->hideOnForm();
    }

    public function configureActions(Actions $actions): Actions
    {
        $retryError = Action::new('retryError', 'Retry Error', 'fas fa-redo')
            ->linkToCrudAction('retryError');
        $removeError = Action::new('removeError', 'Remove Error', 'fas fa-trash')
            ->linkToCrudAction('removeError');

        return $actions
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ->remove(Crud::PAGE_INDEX, Action::EDIT)
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->add(Crud::PAGE_INDEX, $retryError)
            ->add(Crud::PAGE_INDEX, $removeError)
            ->setPermissions(array_fill_keys(
                (new ReflectionClass(Action::class))->getConstants(),
                'ROLE_ADMIN'
            ));
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $queryBuilder = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
        $queryBuilder
            ->andWhere('entity.queueName = :failed')
            ->setParameter('failed', 'failed');

        return $queryBuilder;
    }

    public function retryError(AdminContext $context, KernelInterface $kernel): RedirectResponse
    {
        return $this->runCommand($context, $kernel, 'messenger:failed:retry', 'Error %d is retried', 'Can not retry error %d');
    }

    public function removeError(AdminContext $context, KernelInterface $kernel): RedirectResponse
    {
        return $this->runCommand($context, $kernel, 'messenger:failed:remove', 'Error %d is removed', 'Can not remove error %d');
    }

    protected function runCommand(AdminContext $context, KernelInterface $kernel, string $command, string $successMessage, string $errorMessage): RedirectResponse
    {
        $error = $context->getEntity()->getInstance();

        $application = new Application($kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput([
            'command' => $command,
            'id' => [$error->getId()],
            '--force' => true,
        ]);

        if (!$application->run($input)) {
            $this->addFlash('success', sprintf($successMessage, $error->getId()));
        } else {
            $this->addFlash('error', sprintf($errorMessage, $error->getId()));
        }

        return $this->redirect($this->get(AdminUrlGenerator::class)->setAction(Action::INDEX)->unset(EA::ENTITY_ID)->generateUrl());
    }
}
