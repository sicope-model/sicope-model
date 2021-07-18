<?php

/**
 * This file is part of the SICOPE Model package.
 *
 * @package     sicope-model
 * @license     LICENSE
 * @author      Ramazan APAYDIN <apaydin541@gmail.com>
 * @link        https://github.com/appaydin/pd-admin
 * @author      Tien Xuan Vo <tien.xuan.vo@gmail.com>
 * @link        https://github.com/sicope-model/sicope-model
 */

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class PlacesTransformer implements DataTransformerInterface
{
    /**
     * Transforms places from an array to a string.
     *
     * @param array $placesAsArray
     *
     * @return string
     */
    public function transform($placesAsArray)
    {
        return implode(',', $placesAsArray ?? []);
    }

    /**
     * Transforms places from a string to an array.
     *
     * @param string $placesAsString
     *
     * @return array
     */
    public function reverseTransform($placesAsString)
    {
        return \is_string($placesAsString) && '' !== $placesAsString ? explode(',', $placesAsString) : [];
    }
}
