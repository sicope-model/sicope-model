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

namespace App\Form\Testing;

use App\Form\DataTransformer\FileModelTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Valid;

class ModelImportType extends AbstractType
{
    protected DataTransformerInterface $transformer;

    public function __construct(FileModelTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('model', FileType::class, [
                'label' => 'model_file',
                'attr' => [
                    'label' => 'upload_image_btn',
                    'label_class' => 'btn btn-success',
                ],
                'required' => true,
                'data_class' => null,
                'constraints' => [
                    new Valid(),
                ],
            ])
            ->add('import', SubmitType::class, [
                'label' => 'import',
            ])
        ;

        $builder->get('model')
            ->addModelTransformer($this->transformer);
    }
}
