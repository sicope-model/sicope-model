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

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class CommandDto
{
    /**
     * @Assert\NotBlank
     * @Assert\Type("string")
     */
    public $command;

    /**
     * @Assert\NotBlank
     * @Assert\Type("string")
     */
    public $target;

    /**
     * @Assert\Type("string")
     */
    public $value;
}
