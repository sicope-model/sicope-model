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
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\Valid;
use Tienvx\Bundle\MbtBundle\ValueObject\Model\Transition;

class TransitionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('label', TextType::class, [
                'label' => 'transition_label',
                'attr' => [
                    'class' => 'transition-label',
                ],
                'constraints' => [
                    new NotBlank(),
                    new Type('string'),
                ],
            ])
            ->add('guard', TextType::class, [
                'label' => 'transition_guard',
                'constraints' => [
                    new Type('string'),
                ],
            ])
            ->add('actions', CollectionType::class, [
                'label' => 'transition_actions',
                'entry_type' => CommandType::class,
                'entry_options' => [
                    'label' => false,
                    'attr' => [
                        'class' => 'col list-group-item action',
                    ],
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'attr' => [
                    'class' => 'list-group actions col pl-3',
                ],
                'constraints' => [
                    new Valid(),
                ],
            ])
            ->add('add_action', ButtonType::class, [
                'label' => 'add_action',
                'attr' => [
                    'class' => 'add-action',
                ],
            ])
            ->add('from_places', ChoiceType::class, [
                'label' => 'from_places',
                'attr' => [
                    'class' => 'from-places',
                ],
                'constraints' => [
                    new All([
                        new Type('integer'),
                    ]),
                ],
            ])
            ->add('to_places', CollectionType::class, [
                'label' => 'to_places',
                'entry_type' => ToPlaceType::class,
                'entry_options' => [
                    'label' => false,
                    'attr' => [
                        'class' => 'col list-group-item',
                    ],
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'attr' => [
                    'class' => 'list-group to-places col pl-3',
                ],
                'constraints' => [
                    new Valid(),
                ],
            ])
            ->add('add_place', ButtonType::class, [
                'label' => 'add_place',
                'attr' => [
                    'class' => 'add-place',
                ],
            ])
            ->add('remove_transition', ButtonType::class, [
                'label' => 'remove_transition',
                'attr' => [
                    'class' => 'remove-transition',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Transition::class,
        ]);
    }
}
