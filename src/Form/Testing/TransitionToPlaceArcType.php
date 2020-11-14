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
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Type;

class TransitionToPlaceArcType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('transition', IntegerType::class, [
                'label' => 'arc_transition',
                'constraints' => [
                    new Type('integer'),
                ],
            ])
            ->add('place', IntegerType::class, [
                'label' => 'arc_place',
                'constraints' => [
                    new Type('integer'),
                ],
            ])
            ->add('expression', TextType::class, [
                'label' => 'output_arc_expression',
                'constraints' => [
                    new Type('string'),
                ],
            ])
        ;
    }
}
