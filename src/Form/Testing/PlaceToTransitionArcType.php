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
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Type;

class PlaceToTransitionArcType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('place', IntegerType::class, [
                'label' => 'arc_place',
                'constraints' => [
                    new Type('integer'),
                ],
            ])
            ->add('transition', IntegerType::class, [
                'label' => 'arc_transition',
                'constraints' => [
                    new Type('integer'),
                ],
            ])
        ;
    }
}
