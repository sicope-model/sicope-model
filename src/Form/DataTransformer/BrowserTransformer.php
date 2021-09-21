<?php

/**
 * This file is part of the SICOPE Model package.
 *
 * @package     sicope-model
 * @license     LICENSE
 * @author      Tien Xuan Vo <tien.xuan.vo@gmail.com>
 * @link        https://github.com/sicope-model/sicope-model
 */

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Tienvx\Bundle\MbtBundle\Entity\Task\Browser;
use Tienvx\Bundle\MbtBundle\Model\Task\BrowserInterface;

class BrowserTransformer implements DataTransformerInterface
{
    /**
     * Transforms browser from an object to a string.
     *
     * @param BrowserInterface|null $browserAsObject
     *
     * @return string
     */
    public function transform($browserAsObject)
    {
        return $browserAsObject ? sprintf('%s:%s', $browserAsObject->getName(), $browserAsObject->getVersion()) : null;
    }

    /**
     * Transforms browser from a string to an object.
     *
     * @param string $browserAsString
     *
     * @return BrowserInterface
     */
    public function reverseTransform($browserAsString)
    {
        [$name, $version] = explode(':', $browserAsString);

        $browser = new Browser();
        $browser->setName($name);
        $browser->setVersion($version);

        return $browser;
    }
}
