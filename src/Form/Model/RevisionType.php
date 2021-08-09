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

namespace App\Form\Model;

use Stakovicz\UXCollection\Form\UXCollectionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tienvx\Bundle\MbtBundle\Entity\Model\Revision;

class RevisionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('places', UXCollectionType::class, [
                'label' => 'testing.model_places',
                'entry_type' => PlaceType::class,
                'entry_options' => [
                    'label' => false,
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'button_add' => [
                    'text' => 'testing.add_place',
                    'attr' => ['class' => 'btn btn-secondary'],
                ],
                'button_delete' => [
                    'text' => false,
                    'attr' => ['class' => 'btn-close'],
                ],
            ])
            ->add('transitions', UXCollectionType::class, [
                'label' => 'testing.model_transitions',
                'entry_type' => TransitionType::class,
                'entry_options' => [
                    'label' => false,
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'button_add' => [
                    'text' => 'testing.add_transition',
                    'attr' => ['class' => 'btn btn-secondary'],
                ],
                'button_delete' => [
                    'text' => false,
                    'attr' => ['class' => 'btn-close'],
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
