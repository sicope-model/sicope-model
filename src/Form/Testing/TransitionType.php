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
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Type;

class TransitionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('label', TextType::class, [
                'label' => 'transition_label',
                'constraints' => [
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
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'attr' => [
                    'data-widget-entries' => '<li class="list-group-item"></li>',
                    'class' => 'list-group actions',
                ],
            ])
            ->add('add_action', ButtonType::class, [
                'label' => 'add_action',
                'attr' => [
                    'data-list-selector' => '.list-group.actions',
                    'class' => 'btn-secondary add-action',
                ],
            ])
        ;
    }
}
