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

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tienvx\Bundle\MbtBundle\Entity\Model\Revision;
use Tienvx\UX\CollectionJs\Form\CollectionJsType;

class RevisionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('places', CollectionJsType::class, [
                'label' => 'Places',
                'entry_type' => PlaceType::class,
                'entry_options' => [
                    'label' => false,
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'allow_move_up' => true,
                'allow_move_down' => true,
                'render_expanded' => false,
            ])
            ->add('transitions', CollectionJsType::class, [
                'label' => 'Transitions',
                'entry_type' => TransitionType::class,
                'entry_options' => [
                    'label' => false,
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'allow_move_up' => true,
                'allow_move_down' => true,
                'render_expanded' => false,
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
