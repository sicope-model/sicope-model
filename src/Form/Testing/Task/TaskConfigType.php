<?php

/**
 * This file is part of the SICOPE Model package.
 *
 * @package     sicope-model
 * @license     LICENSE
 * @author      Ramazan APAYDIN <apaydin541@gmail.com>
 * @author      Tien Xuan Vo <tien.xuan.vo@gmail.com>
 * @link        https://github.com/sicope-model/sicope-model
 */

namespace App\Form\Testing\Task;

use App\Model\GeneratorInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tienvx\Bundle\MbtBundle\Channel\ChannelManager;
use Tienvx\Bundle\MbtBundle\Entity\Task\TaskConfig;
use Tienvx\Bundle\MbtBundle\Generator\GeneratorManager;
use Tienvx\Bundle\MbtBundle\Reducer\ReducerManager;

class TaskConfigType extends AbstractType
{
    protected GeneratorManager $generatorManager;
    protected ReducerManager $reducerManager;
    protected ChannelManager $channelManager;

    public function __construct(
        GeneratorManager $generatorManager,
        ReducerManager $reducerManager,
        ChannelManager $channelManager
    ) {
        $this->generatorManager = $generatorManager;
        $this->reducerManager = $reducerManager;
        $this->channelManager = $channelManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $formModifier = function (FormInterface $form, ?string $generator = null) {
            $generators = $this->generatorManager->all();
            $generator = \in_array($generator, $generators) ? $generator : reset($generators);

            $form->add('generator', ChoiceType::class, [
                'label' => 'task_generator',
                'choices' => $this->generatorManager->all(),
                'choice_label' => fn ($generator) => $generator,
                'attr' => [
                    'class' => 'generators',
                ],
                'data' => $generator,
            ]);

            if ($this->generatorManager->get($generator) instanceof GeneratorInterface) {
                $form->add('generatorConfig', $this->generatorManager->get($generator)->getConfigFormType(), [
                    'label' => 'task_generator_config',
                    'attr' => [
                        'class' => 'col list-group-item generator-config',
                    ],
                ]);
            }

            $form->add('reducer', ChoiceType::class, [
                'label' => 'task_reducer',
                'choices' => $this->reducerManager->all(),
                'attr' => [
                    'class' => 'reducers',
                ],
                'choice_label' => fn ($reducer) => $reducer,
            ]);

            $form->add('notifyAuthor', CheckboxType::class, [
                'label' => 'task_notify_author',
                'required' => false,
                'help' => 'task_notify_author_info',
            ]);

            $form->add('notifyChannels', ChoiceType::class, [
                'label' => 'task_notify_channels',
                'choices' => $this->channelManager->all(),
                'multiple' => true,
                'required' => false,
                'attr' => [
                    'class' => 'channels',
                ],
                'choice_label' => fn ($channel) => $channel,
            ]);
        };

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier) {
                $formModifier($event->getForm());
            }
        );

        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) use ($formModifier) {
                $data = $event->getData();

                $formModifier(
                    $event->getForm(),
                    $data['generator'] ?? null
                );
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TaskConfig::class,
        ]);
    }
}
