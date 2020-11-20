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

class ModelDto
{
    /**
     * @Assert\NotBlank
     * @Assert\Type("string")
     */
    public $label;

    /**
     * @Assert\All({
     *     @Assert\NotBlank,
     *     @Assert\Type("string")
     * })
     */
    public $tags;

    /**
     * @Assert\All({
     *     @Assert\Valid
     * })
     */
    public $places;

    /**
     * @Assert\All({
     *     @Assert\Valid
     * })
     */
    public $transitions;
}
