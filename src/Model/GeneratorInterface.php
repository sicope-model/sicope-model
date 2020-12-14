<?php

namespace App\Model;

use Tienvx\Bundle\MbtBundle\Generator\GeneratorInterface as BaseGeneratorInterface;

interface GeneratorInterface extends BaseGeneratorInterface
{
    public function getConfigFormType(): string;
}
