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

namespace App\Form\Testing\Model\Revision;

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
                'label' => 'testing.command_command',
                'choices' => array_combine($commands, $commands),
                'choice_translation_domain' => 'commands',
                'attr' => [
                    'class' => 'select-command',
                ],
            ])
            ->add('target', TextType::class, [
                'label' => 'testing.command_target',
                'required' => false,
            ])
            ->add('value', TextType::class, [
                'label' => 'testing.command_value',
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
