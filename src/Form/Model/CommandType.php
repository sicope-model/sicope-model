<?php

/**
 * This file is part of the SICOPE Model package.
 *
 * @package     sicope-model
 * @license     LICENSE
 * @author      Tien Xuan Vo <tien.xuan.vo@gmail.com>
 * @link        https://github.com/sicope-model/sicope-model
 */

namespace App\Form\Model;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tienvx\Bundle\MbtBundle\Command\CommandRunnerManagerInterface;
use Tienvx\Bundle\MbtBundle\ValueObject\Model\Command;

class CommandType extends AbstractType
{
    public function __construct(
        private CommandRunnerManagerInterface $commandRunnerManager,
        private TranslatorInterface $translator
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $commands = $this->commandRunnerManager->getAllCommands();
        $builder
            ->add('command', ChoiceType::class, [
                'label' => 'Command',
                'choices' => array_combine(array_map(fn (string $command) => $this->translator->trans($command), $commands), $commands),
                'label_attr' => ['class' => 'required'],
                'attr' => [
                    'required' => 'required',
                    'data-controller' => 'select',
                ],
            ])
            ->add('target', TextType::class, [
                'label' => 'Target',
                'required' => false,
            ])
            ->add('value', TextType::class, [
                'label' => 'Value',
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
