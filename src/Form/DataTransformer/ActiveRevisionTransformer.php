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
use Symfony\Component\Form\Exception\TransformationFailedException;
use Tienvx\Bundle\MbtBundle\Entity\Model;
use Tienvx\Bundle\MbtBundle\Model\Model\RevisionInterface;

class ActiveRevisionTransformer implements DataTransformerInterface
{
    /**
     * Do nothing.
     *
     * @return null
     */
    public function transform($revision)
    {
        return null;
    }

    /**
     * Transforms a Model object to Revision object.
     *
     * @param Model $model
     */
    public function reverseTransform($model): RevisionInterface
    {
        if (!$model instanceof Model) {
            throw new TransformationFailedException('Model is require to be transformed');
        }

        return $model->getActiveRevision();
    }
}
