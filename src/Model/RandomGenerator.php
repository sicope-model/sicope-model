<?php

namespace App\Model;

use App\Form\Testing\Task\RandomGeneratorConfigType;
use Tienvx\Bundle\MbtBundle\Generator\RandomGenerator as BaseRandomGenerator;

class RandomGenerator extends BaseRandomGenerator implements GeneratorInterface
{
    public function getConfigFormType(): string
    {
        return RandomGeneratorConfigType::class;
    }
}
