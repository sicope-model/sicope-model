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

class FromPlacesTransformer implements DataTransformerInterface
{
    /**
     * Transforms from-places from an array to a string.
     *
     * @param array $fromPlacesAsArray
     *
     * @return string
     */
    public function transform($fromPlacesAsArray)
    {
        return implode(',', $fromPlacesAsArray ?? []);
    }

    /**
     * Transforms from-places from a string to an array.
     *
     * @param string $fromPlacesAsString
     *
     * @return array
     */
    public function reverseTransform($fromPlacesAsString)
    {
        return explode(',', $fromPlacesAsString);
    }
}
