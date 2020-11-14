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
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Type;

class PlaceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('label', TextType::class, [
                'label' => 'place_label',
                'constraints' => [
                    new Type('string'),
                ],
            ])
            ->add('init', CheckboxType::class, [
                'label' => 'place_init',
                'constraints' => [
                    new Type('boolean'),
                ],
            ])
            ->add('assertions', CollectionType::class, [
                'label' => 'place_assertions',
                'entry_type' => CommandType::class,
                'entry_options' => [
                    'label' => false,
                    'attr' => [
                        'class' => 'col',
                    ],
                ],
                'allow_add' => true,
                'attr' => [
                    'data-widget-entries' => '<li class="list-group-item"></li>',
                    'class' => 'list-group assertions',
                ],
            ])
            ->add('add_assertion', ButtonType::class, [
                'label' => 'add_assertion',
                'attr' => [
                    'data-list-selector' => '.list-group.assertions',
                    'class' => 'btn-secondary add-assertion',
                ],
            ])
        ;
    }
}
