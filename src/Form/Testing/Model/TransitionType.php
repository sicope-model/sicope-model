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

use App\Form\DataTransformer\PlacesTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tienvx\Bundle\MbtBundle\ValueObject\Model\Transition;

class TransitionType extends AbstractType
{
    protected DataTransformerInterface $transformer;

    public function __construct(PlacesTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('label', TextType::class, [
                'label' => 'transition_label',
                'attr' => [
                    'class' => 'transition-label',
                ],
            ])
            ->add('guard', TextType::class, [
                'label' => 'transition_guard',
                'required' => false,
            ])
            ->add('expression', TextType::class, [
                'label' => 'transition_expression',
                'required' => false,
            ])
            ->add('actions', CollectionType::class, [
                'label' => 'transition_actions',
                'entry_type' => CommandType::class,
                'entry_options' => [
                    'label' => false,
                    'attr' => [
                        'class' => 'col list-group-item action',
                        'delete_class' => 'remove-command',
                    ],
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'attr' => [
                    'class' => 'list-group actions col pl-3',
                ],
            ])
            ->add('add_action', ButtonType::class, [
                'label' => 'add_action',
                'attr' => [
                    'class' => 'add-action',
                ],
            ])
            ->add('from_places', TextType::class, [
                'label' => 'from_places',
                'attr' => [
                    'class' => 'select-from-places',
                ],
            ])
            ->add('to_places', TextType::class, [
                'label' => 'to_places',
                'attr' => [
                    'class' => 'select-to-places',
                ],
            ])
        ;

        $builder->get('from_places')
            ->addModelTransformer($this->transformer);
        $builder->get('to_places')
            ->addModelTransformer($this->transformer);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Transition::class,
        ]);
    }
}
