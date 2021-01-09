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

use App\Form\Testing\Model\PlaceType;
use App\Form\Testing\Model\TransitionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tienvx\Bundle\MbtBundle\Entity\Model;

class ModelType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('label', TextType::class, [
                'label' => 'model_label',
            ])
            ->add('tags', TextType::class, [
                'label' => 'model_tags',
                'attr' => [
                    'data-tags' => '',
                ],
                'required' => false,
            ])
            ->add('start_url', UrlType::class, [
                'label' => 'model_start_url',
            ])
            ->add('start_expression', TextType::class, [
                'label' => 'model_start_expression',
            ])
            ->add('places', CollectionType::class, [
                'label' => 'model_places',
                'entry_type' => PlaceType::class,
                'entry_options' => [
                    'label' => false,
                    'attr' => [
                        'class' => 'col list-group-item place',
                        'delete_class' => 'remove-place',
                    ],
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'attr' => [
                    'class' => 'list-group places col pl-3',
                ],
            ])
            ->add('add_place', ButtonType::class, [
                'label' => 'add_place',
                'attr' => [
                    'class' => 'add-place',
                ],
            ])
            ->add('transitions', CollectionType::class, [
                'label' => 'model_transitions',
                'entry_type' => TransitionType::class,
                'entry_options' => [
                    'label' => false,
                    'attr' => [
                        'class' => 'col list-group-item transition',
                        'delete_class' => 'remove-transition',
                    ],
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'attr' => [
                    'class' => 'list-group transitions col pl-3',
                ],
            ])
            ->add('add_transition', ButtonType::class, [
                'label' => 'add_transition',
                'attr' => [
                    'class' => 'add-transition',
                ],
            ])
            ->add('save', SubmitType::class, [
                'label' => 'save',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Model::class,
        ]);
    }
}
