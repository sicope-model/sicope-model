<?php

/**
 * This file is part of the SICOPE Model package.
 *
 * @package     sicope-model
 * @license     LICENSE
 * @author      Ramazan APAYDIN <apaydin541@gmail.com>
 * @link        https://github.com/appaydin/pd-admin
 * @author      Tien Xuan Vo <tien.xuan.vo@gmail.com>
 * @link        https://github.com/sicope-model/sicope-model
 */

namespace App\Controller;

use App\Entity\Config;
use App\Service\ConfigBag;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use Symfony\Component\Form\FormInterface;
use Tienvx\Bundle\MbtBundle\Channel\ChannelManagerInterface;
use Tienvx\Bundle\MbtBundle\Generator\GeneratorManagerInterface;
use Tienvx\Bundle\MbtBundle\Reducer\ReducerManagerInterface;

/**
 * Controller managing the testing config.
 *
 * @author Tien Xuan Vo <tien.xuan.vo@gmail.com>
 */
class ConfigCrudController extends AbstractCrudController
{
    const ACTION = 'save';

    public function __construct(
        private GeneratorManagerInterface $generatorManager,
        private ReducerManagerInterface $reducerManager,
        private ChannelManagerInterface $channelManager,
        private ConfigBag $bag
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return Config::class;
    }

    public function configureFields(string $pageName): iterable
    {
        $generators = $this->generatorManager->all();
        $reducers = $this->reducerManager->all();
        $channels = $this->channelManager->all();
        yield ChoiceField::new('generator', 'Generator')->setChoices(array_combine($generators, $generators))->setRequired(true);
        yield ChoiceField::new('reducer', 'Reducer')->setChoices(array_combine($reducers, $reducers))->setRequired(true);
        yield BooleanField::new('report_bug', 'Report Bug');
        yield BooleanField::new('notify_author', 'Notify Author');
        yield ChoiceField::new('notify_channels', 'Notify Channels')->setChoices(array_combine($channels, $channels))->allowMultipleChoices();
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions->remove(Action::EDIT, Action::SAVE_AND_RETURN);
    }

    public function createEditForm(EntityDto $entityDto, KeyValueStore $formOptions, AdminContext $context): FormInterface
    {
        $formOptions->set('data_class', null);
        return parent::createEditForm($entityDto, $formOptions, $context)->setData($this->bag->getAll());
    }

    protected function processUploadedFiles(FormInterface $form): void
    {
        $this->bag->saveForm($form);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
    }
}
