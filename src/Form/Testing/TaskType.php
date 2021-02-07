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

namespace App\Form\Testing;

use App\Form\DataTransformer\ActiveRevisionTransformer;
use App\Form\DataTransformer\TitleTransformer;
use App\Form\Testing\Task\SeleniumConfigType;
use App\Form\Testing\Task\TaskConfigType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tienvx\Bundle\MbtBundle\Entity\Model;
use Tienvx\Bundle\MbtBundle\Entity\Task;

class TaskType extends AbstractType
{
    protected DataTransformerInterface $revisionTransformer;
    protected DataTransformerInterface $titleTransformer;

    public function __construct(ActiveRevisionTransformer $revisionTransformer, TitleTransformer $titleTransformer)
    {
        $this->revisionTransformer = $revisionTransformer;
        $this->titleTransformer = $titleTransformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'task_title',
            ])
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($builder) {
            $task = $event->getData();
            $form = $event->getForm();

            if (!$task || null === $task->getId()) {
                $form->add(
                    $builder
                        ->create('modelRevision', EntityType::class, [
                            'class' => Model::class,
                            'label' => 'task_model',
                            'choice_label' => 'label',
                            'auto_initialize' => false,
                        ])
                        ->addModelTransformer($this->revisionTransformer)
                        ->getForm()
                );
            }

            $form
                ->add('seleniumConfig', SeleniumConfigType::class, [
                    'label' => 'task_selenium_config',
                    'attr' => [
                        'class' => 'col list-group-item',
                    ],
                ])
                ->add('taskConfig', TaskConfigType::class, [
                    'label' => 'task_task_config',
                    'attr' => [
                        'class' => 'col list-group-item',
                    ],
                ])
                ->add('save', SubmitType::class, [
                    'label' => 'save',
                ]);
        });

        $builder->get('title')
            ->addModelTransformer($this->titleTransformer);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
        ]);
    }
}
