<?php

namespace App\Form\Testing\Task;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Tienvx\Bundle\MbtBundle\Generator\RandomGenerator;

class RandomGeneratorConfigType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(RandomGenerator::MAX_PLACE_COVERAGE, NumberType::class, [
            'label' => 'task_place_coverage',
            'html5' => true,
            'data' => 100,
        ]);
        $builder->add(RandomGenerator::MAX_TRANSITION_COVERAGE, NumberType::class, [
            'label' => 'task_transition_coverage',
            'html5' => true,
            'data' => 100,
        ]);
    }
}
