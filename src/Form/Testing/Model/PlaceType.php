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

namespace App\Form\Testing\Model;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tienvx\Bundle\MbtBundle\ValueObject\Model\Place;

class PlaceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('label', TextType::class, [
                'label' => 'place_label',
                'attr' => [
                    'class' => 'place-label',
                ],
            ])
            ->add('start', CheckboxType::class, [
                'label' => 'place_start',
                'required' => false,
            ])
            ->add('commands', CollectionType::class, [
                'label' => 'commands',
                'entry_type' => CommandType::class,
                'entry_options' => [
                    'label' => false,
                    'attr' => [
                        'class' => 'col list-group-item command',
                    ],
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'attr' => [
                    'class' => 'list-group commands col pl-3',
                ],
                'prototype_name' => '__command__',
            ])
            ->add('add_command', ButtonType::class, [
                'label' => 'add_command',
                'attr' => [
                    'class' => 'add-command btn-secondary',
                ],
            ])
            ->add('remove_place', ButtonType::class, [
                'attr' => [
                    'class' => 'close remove-place',
                    'aria-label' => 'Close',
                ],
                'label' => '<span aria-hidden="true">&times;</span>',
                'label_html' => true,
                'translation_domain' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Place::class,
        ]);
    }
}
