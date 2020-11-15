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

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

class ModelType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('label', TextType::class, [
                'label' => 'model_label',
                'constraints' => [
                    new NotBlank(),
                    new Type('string'),
                ],
            ])
            ->add('tags', TextType::class, [
                'label' => 'model_tags',
                'attr' => [
                    'data-tags' => '',
                ],
            ])
            ->add('places', CollectionType::class, [
                'label' => 'model_places',
                'entry_type' => PlaceType::class,
                'entry_options' => [
                    'label' => false,
                    'attr' => [
                        'class' => 'col',
                    ],
                ],
                'allow_add' => true,
                'attr' => [
                    'data-widget-entries' => '<li class="list-group-item"></li>',
                    'class' => 'list-group places',
                ],
            ])
            ->add('add_place', ButtonType::class, [
                'label' => 'add_place',
                'attr' => [
                    'data-list-selector' => '.list-group.places',
                    'class' => 'add-place',
                ],
            ])
            ->add('transitions', CollectionType::class, [
                'label' => 'model_transitions',
                'entry_type' => TransitionType::class,
                'entry_options' => [
                    'label' => false,
                    'attr' => [
                        'class' => 'col',
                    ],
                ],
                'allow_add' => true,
                'attr' => [
                    'data-widget-entries' => '<li class="list-group-item"></li>',
                    'class' => 'list-group transitions',
                ],
            ])
            ->add('add_transition', ButtonType::class, [
                'label' => 'add_transition',
                'attr' => [
                    'data-list-selector' => '.list-group.transitions',
                    'class' => 'add-transition',
                ],
            ])
            ->add('place_to_transition_arcs', CollectionType::class, [
                'label' => 'model_place_to_transition_arcs',
                'entry_type' => PlaceToTransitionArcType::class,
                'entry_options' => [
                    'label' => false,
                    'attr' => [
                        'class' => 'col',
                    ],
                ],
                'allow_add' => true,
                'attr' => [
                    'data-widget-entries' => '<li class="list-group-item"></li>',
                    'class' => 'list-group place-to-transition-arcs',
                ],
            ])
            ->add('add_place_to_transition_arc', ButtonType::class, [
                'label' => 'add_arc',
                'attr' => [
                    'data-list-selector' => '.list-group.place-to-transition-arcs',
                    'class' => 'add-arc',
                ],
            ])
            ->add('transition_to_place_arcs', CollectionType::class, [
                'label' => 'model_transition_to_place_arcs',
                'entry_type' => TransitionToPlaceArcType::class,
                'entry_options' => [
                    'label' => false,
                    'attr' => [
                        'class' => 'col',
                    ],
                ],
                'allow_add' => true,
                'attr' => [
                    'data-widget-entries' => '<li class="list-group-item"></li>',
                    'class' => 'list-group transition-to-place-arcs',
                ],
            ])
            ->add('add_transition_to_place_arc', ButtonType::class, [
                'label' => 'add_arc',
                'attr' => [
                    'data-list-selector' => '.list-group.transition-to-place-arcs',
                    'class' => 'add-arc',
                ],
            ])
            ->add('save', SubmitType::class, [
                'label' => 'save',
            ])
        ;
    }
}
