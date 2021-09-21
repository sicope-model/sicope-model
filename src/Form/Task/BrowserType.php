<?php

/**
 * This file is part of the SICOPE Model package.
 *
 * @package     sicope-model
 * @license     LICENSE
 * @author      Tien Xuan Vo <tien.xuan.vo@gmail.com>
 * @link        https://github.com/sicope-model/sicope-model
 */

namespace App\Form\Task;

use App\Form\DataTransformer\BrowserTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class BrowserType extends AbstractType
{
    protected DataTransformerInterface $transformer;

    public function __construct(BrowserTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->addModelTransformer($this->transformer);
    }

    public function getParent()
    {
        return ChoiceType::class;
    }
}
