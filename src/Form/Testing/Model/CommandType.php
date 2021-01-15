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

namespace App\Form\Testing\Model;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tienvx\Bundle\MbtBundle\Command\CommandRunnerManagerInterface;
use Tienvx\Bundle\MbtBundle\ValueObject\Model\Command;

class CommandType extends AbstractType
{
    protected CommandRunnerManagerInterface $commandRunnerManager;

    public function __construct(CommandRunnerManagerInterface $commandRunnerManager)
    {
        $this->commandRunnerManager = $commandRunnerManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $commands = $this->commandRunnerManager->getAllCommands();
        $builder
            ->add('command', ChoiceType::class, [
                'label' => 'command_command',
                'choices' => array_combine($commands, $commands),
                'choice_translation_domain' => 'commands',
                'attr' => [
                    'class' => 'select-command',
                ],
            ])
            ->add('target', TextType::class, [
                'label' => 'command_target',
                'required' => false,
            ])
            ->add('value', TextType::class, [
                'label' => 'command_value',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Command::class,
        ]);
    }
}
