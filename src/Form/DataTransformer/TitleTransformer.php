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

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class TitleTransformer implements DataTransformerInterface
{
    /**
     * @param $title
     *
     * @return string
     */
    public function transform($title)
    {
        return $title ?? '';
    }

    /**
     * @param $title
     *
     * @return string
     */
    public function reverseTransform($title)
    {
        return $this->transform($title);
    }
}
