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

use App\Form\DataTransformer\PlacesTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
                'label' => 'Label',
                // Workaround for https://github.com/EasyCorp/EasyAdminBundle/issues/1124
                'label_attr' => ['class' => 'required'],
                'attr' => [
                    'required' => true,
                ],
            ])
            ->add('guard', TextType::class, [
                'label' => 'Guard',
                'required' => false,
            ])
            ->add('commands', CollectionType::class, [
                'label' => 'Commands',
                'entry_type' => CommandType::class,
                'entry_options' => [
                    'label' => false,
                ],
                'allow_add' => true,
                'allow_delete' => true,
            ])
            ->add('fromPlaces', TextType::class, [
                'label' => 'From Places',
                'attr' => [
                    'data-controller' => 'places-select',
                    'data-places-target' => 'placesSelect',
                ],
                'required' => true,
            ])
            ->add('toPlaces', TextType::class, [
                'label' => 'To Places',
                'attr' => [
                    'data-controller' => 'places-select',
                    'data-places-target' => 'placesSelect',
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
