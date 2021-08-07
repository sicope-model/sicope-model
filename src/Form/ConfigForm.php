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

namespace App\Form;

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
                'label' => 'generator',
                'choices' => $this->generatorManager->all(),
                'choice_label' => fn ($generator) => $generator,
                'empty_data' => 'testing.generator_required',
                'placeholder' => false,
                'required' => true,
            ])
            ->add('reducer', ChoiceType::class, [
                'label' => 'reducer',
                'choices' => $this->reducerManager->all(),
                'choice_label' => fn ($reducer) => $reducer,
                'empty_data' => 'testing.reducer_required',
                'placeholder' => false,
                'required' => true,
            ])
            ->add('report_bug', CheckboxType::class, [
                'label' => 'report_bug',
                'required' => false,
            ])
            ->add('notify_author', CheckboxType::class, [
                'label' => 'notify_author',
                'required' => false,
            ])
            ->add('notify_channels', ChoiceType::class, [
                'label' => 'notify_channels',
                'choices' => $this->channelManager->all(),
                'choice_label' => fn ($channel) => $channel,
                'empty_data' => 'testing.no_channels',
                'placeholder' => false,
                'multiple' => true,
                'required' => false,
                'expanded' => false,
                'attr' => [
                    'class' => 'field-select'
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'save',
            ]);
    }

    public function getBlockPrefix(): string
    {
        return '';
    }
}
