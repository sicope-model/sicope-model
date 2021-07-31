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

namespace App\Form\Testing;

use App\Form\DataTransformer\ActiveRevisionTransformer;
use App\Form\DataTransformer\TitleTransformer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tienvx\Bundle\MbtBundle\Entity\Model;
use Tienvx\Bundle\MbtBundle\Entity\Task;

class TaskType extends AbstractType
{
    protected DataTransformerInterface $revisionTransformer;
    protected DataTransformerInterface $titleTransformer;

    public function __construct(ActiveRevisionTransformer $revisionTransformer, TitleTransformer $titleTransformer)
    {
        $this->revisionTransformer = $revisionTransformer;
        $this->titleTransformer = $titleTransformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'testing.task_title',
            ])
            ->add('modelRevision', EntityType::class, [
                'class' => Model::class,
                'label' => 'testing.task_model',
                'choice_label' => 'label',
                'auto_initialize' => false,
            ])
            ->add('browser', ChoiceType::class, [
                'label' => 'testing.task_browser',
                'choices' => [],
                'choice_label' => fn ($browser) => $browser,
            ])
            ->add('browserVersion', ChoiceType::class, [
                'label' => 'testing.task_browser_version',
                'choices' => [],
                'choice_label' => fn ($version) => $version,
            ])
            ->add('save', SubmitType::class, [
                'label' => 'save',
            ]);
        ;

        $builder->get('modelRevision')
            ->addModelTransformer($this->revisionTransformer);
        $builder->get('title')
            ->addModelTransformer($this->titleTransformer);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
        ]);
    }
}
