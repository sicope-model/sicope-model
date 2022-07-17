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

use App\Service\Config;
use Craue\ConfigBundle\Entity\Setting;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Form\FormInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tienvx\Bundle\MbtBundle\Channel\ChannelManagerInterface;
use Tienvx\Bundle\MbtBundle\Generator\GeneratorManagerInterface;
use Tienvx\Bundle\MbtBundle\Reducer\ReducerManagerInterface;

#[IsGranted('ROLE_ADMIN')]
class ConfigCrudController extends AbstractCrudController
{
    public function __construct(
        private GeneratorManagerInterface $generatorManager,
        private ReducerManagerInterface $reducerManager,
        private ChannelManagerInterface $channelManager,
        private Config $config,
        private TranslatorInterface $translator
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return Setting::class;
    }

    public function configureFields(string $pageName): iterable
    {
        $generators = $this->generatorManager->all();
        $reducers = $this->reducerManager->all();
        $channels = $this->channelManager->all();
        yield ChoiceField::new(Config::GENERATOR, 'Generator')
            ->setChoices($this->translate($generators))
            ->setRequired(true);
        yield ChoiceField::new(Config::REDUCER, 'Reducer')->setChoices($this->translate($reducers))->setRequired(true);
        yield BooleanField::new(Config::REPORT_BUG, 'Report Bug');
        yield BooleanField::new(Config::NOTIFY_AUTHOR, 'Notify Author');
        yield ChoiceField::new(Config::NOTIFY_CHANNELS, 'Notify Channels')
            ->setChoices($this->translate($channels))
            ->allowMultipleChoices();
        yield TextField::new(Config::EMAIL_SENDER, 'Email Sender');
        yield IntegerField::new(Config::MAX_STEPS, 'Max Steps');
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions->remove(Action::EDIT, Action::SAVE_AND_RETURN);
    }

    public function createEditForm(
        EntityDto $entityDto,
        KeyValueStore $formOptions,
        AdminContext $context
    ): FormInterface {
        $formOptions->set('data_class', null);

        return parent::createEditForm($entityDto, $formOptions, $context)->setData($this->config->getAll());
    }

    protected function processUploadedFiles(FormInterface $form): void
    {
        $this->config->saveForm($form);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
    }

    protected function translate(array $choices): array
    {
        return array_combine(array_map(fn (string $choice) => $this->translator->trans($choice), $choices), $choices);
    }
}
