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
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tienvx\Bundle\MbtBundle\ValueObject\Model\ToPlace;

class ToPlaceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('place', TextType::class, [
                'label' => 'place',
                'attr' => [
                    'class' => 'select-to-place',
                ],
            ])
            ->add('expression', TextType::class, [
                'label' => 'expression',
                'required' => false,
            ])
            ->add('remove_place', ButtonType::class, [
                'label' => 'remove_place',
                'attr' => [
                    'class' => 'remove-place',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ToPlace::class,
        ]);
    }
}
