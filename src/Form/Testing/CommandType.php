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

use App\Dto\CommandDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Type;

class CommandType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('command', TextType::class, [
                'label' => 'command_command',
                'constraints' => [
                    new Type('string'),
                ],
            ])
            ->add('target', TextType::class, [
                'label' => 'command_target',
                'constraints' => [
                    new Type('string'),
                ],
            ])
            ->add('value', TextType::class, [
                'label' => 'command_value',
                'constraints' => [
                    new Type('string'),
                ],
            ])
            ->add('remove_command', ButtonType::class, [
                'label' => 'remove_command',
                'attr' => [
                    'class' => 'remove-command',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CommandDto::class,
        ]);
    }
}
