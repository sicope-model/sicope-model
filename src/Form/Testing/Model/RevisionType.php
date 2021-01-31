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

use App\Form\Testing\Model\Revision\PlaceType;
use App\Form\Testing\Model\Revision\TransitionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tienvx\Bundle\MbtBundle\Entity\Model\Revision;

class RevisionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('places', CollectionType::class, [
                'label' => 'model_places',
                'entry_type' => PlaceType::class,
                'entry_options' => [
                    'label' => false,
                    'attr' => [
                        'class' => 'col list-group-item place',
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
                    'class' => 'add-place btn-secondary',
                ],
            ])
            ->add('transitions', CollectionType::class, [
                'label' => 'model_transitions',
                'entry_type' => TransitionType::class,
                'entry_options' => [
                    'label' => false,
                    'attr' => [
                        'class' => 'col list-group-item transition',
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
                    'class' => 'add-transition btn-secondary',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Revision::class,
        ]);
    }
}
