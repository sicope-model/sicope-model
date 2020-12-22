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

use Tienvx\Bundle\MbtBundle\Generator\GeneratorInterface as BaseGeneratorInterface;

interface GeneratorInterface extends BaseGeneratorInterface
{
    public function getConfigFormType(): string;
}
