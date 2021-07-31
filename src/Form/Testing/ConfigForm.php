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

namespace App\Form\Testing;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Tienvx\Bundle\MbtBundle\Channel\ChannelManagerInterface;
use Tienvx\Bundle\MbtBundle\Generator\GeneratorManagerInterface;
use Tienvx\Bundle\MbtBundle\Reducer\ReducerManagerInterface;

/**
 * Testing Config Form.
 *
 * @author Tien Xuan Vo <tien.xuan.vo@gmail.com>
 */
class ConfigForm extends AbstractType
{
    public function __construct(
        private GeneratorManagerInterface $generatorManager,
        private ReducerManagerInterface $reducerManager,
        private ChannelManagerInterface $channelManager
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('generator', ChoiceType::class, [
                'label' => 'testing.generator',
                'help' => 'testing.generator_help',
                'choices' => $this->generatorManager->all(),
                'choice_label' => fn ($generator) => sprintf('testing.%s', $generator),
                'empty_data' => 'testing.generator_required',
                'choice_translation_domain' => false,
                'placeholder' => false,
                'required' => true,
            ])
            ->add('reducer', ChoiceType::class, [
                'label' => 'testing.reducer',
                'help' => 'testing.reducer_help',
                'choices' => $this->reducerManager->all(),
                'choice_label' => fn ($reducer) => sprintf('testing.%s', $reducer),
                'empty_data' => 'testing.reducer_required',
                'choice_translation_domain' => false,
                'placeholder' => false,
                'required' => true,
            ])
            ->add('report_bug', CheckboxType::class, [
                'label' => 'testing.report_bug',
                'help' => 'testing.report_bug_help',
                'label_attr' => ['class' => 'checkbox-switch'],
                'required' => false,
            ])
            ->add('notify_author', CheckboxType::class, [
                'label' => 'testing.notify_author',
                'help' => 'testing.notify_author_help',
                'label_attr' => ['class' => 'checkbox-switch'],
                'required' => false,
            ])
            ->add('notify_channels', ChoiceType::class, [
                'label' => 'testing.notify_channels',
                'help' => 'testing.notify_channels_help',
                'choices' => $this->channelManager->all(),
                'choice_label' => fn ($channel) => sprintf('testing.%s', $channel),
                'empty_data' => 'testing.no_channels',
                'choice_translation_domain' => false,
                'placeholder' => false,
                'multiple' => true,
                'required' => false,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'button.save',
            ]);
    }

    public function getBlockPrefix(): string
    {
        return '';
    }
}
