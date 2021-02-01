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
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Tienvx\Bundle\MbtBundle\Factory\ModelFactory;
use Tienvx\Bundle\MbtBundle\Model\ModelInterface;

class FileModelTransformer implements DataTransformerInterface
{
    /**
     * Do nothing.
     *
     * @param null $modelAsObject
     */
    public function transform($modelAsObject)
    {
        return null;
    }

    /**
     * Transforms a JSON file to a Model object.
     *
     * @param UploadedFile $modelAsJsonFile
     */
    public function reverseTransform($modelAsJsonFile): ModelInterface
    {
        if (!$modelAsJsonFile instanceof UploadedFile) {
            throw new TransformationFailedException('Uploaded file is require to be transformed');
        }

        $data = json_decode($modelAsJsonFile->getContent(), true);
        if (\JSON_ERROR_NONE !== json_last_error() || !\is_array($data)) {
            throw new TransformationFailedException('Uploaded file does not contains valid JSON');
        }

        return ModelFactory::createFromArray($data);
    }
}
