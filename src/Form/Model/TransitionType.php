<?php

/**
 * This file is part of the SICOPE Model package.
 *
 * @package     sicope-model
 * @license     LICENSE
 * @author      Tien Xuan Vo <tien.xuan.vo@gmail.com>
 * @link        https://github.com/sicope-model/sicope-model
 */

namespace App\Form\Model;

use App\Form\DataTransformer\PlacesTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tienvx\Bundle\MbtBundle\ValueObject\Model\Transition;
use Tienvx\UX\CollectionJs\Form\CollectionJsType;

class TransitionType extends AbstractType
{
    public function __construct(private PlacesTransformer $transformer)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $controller = 'places';
        $builder
            ->add('label', TextType::class, [
                'label' => 'Label',
                // Workaround for https://github.com/EasyCorp/EasyAdminBundle/issues/1124
                'label_attr' => ['class' => 'required'],
                'attr' => [
                    'required' => true,
                    'data-controller' => 'accordion-label',
                ],
            ])
            ->add('guard', TextType::class, [
                'label' => 'Guard',
                'required' => false,
            ])
            ->add('expression', TextType::class, [
                'label' => 'Expression',
                'required' => false,
            ])
            ->add('commands', CollectionJsType::class, [
                'label' => 'Commands',
                'entry_type' => CommandType::class,
                'entry_options' => [
                    'label' => false,
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'allow_move_up' => true,
                'allow_move_down' => true,
                'required' => false,
                'prototype_name' => '__command__',
            ])
            ->add('fromPlaces', TextType::class, [
                'label' => 'From Places',
                'attr' => [
                    'class' => 'places-select',
                    "data-{$controller}-target" => 'placesSelect',
                ],
                'required' => false,
            ])
            ->add('toPlaces', TextType::class, [
                'label' => 'To Places',
                'attr' => [
                    'class' => 'places-select',
                    "data-{$controller}-target" => 'placesSelect',
                ],
                'required' => true,
            ])
        ;

        $builder->get('fromPlaces')
            ->addModelTransformer($this->transformer);
        $builder->get('toPlaces')
            ->addModelTransformer($this->transformer);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Transition::class,
        ]);
    }
}
