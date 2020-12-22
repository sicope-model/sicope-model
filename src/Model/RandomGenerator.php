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
