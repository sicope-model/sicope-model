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
use Tienvx\Bundle\MbtBundle\Command\CommandManagerInterface;
use Tienvx\Bundle\MbtBundle\ValueObject\Model\Command;

class CommandType extends AbstractType
{
    public function __construct(private TranslatorInterface $translator)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $commands = array_keys(CommandManagerInterface::COMMANDS);
        $builder
            ->add('command', ChoiceType::class, [
                'label' => 'Command',
                'choices' => array_combine(
                    array_map(
                        fn (string $command) => $this->translator->trans($command),
                        $commands,
                    ),
                    $commands
                ),
                'group_by' => function ($choice) {
                    return $this->translator->trans(CommandManagerInterface::COMMANDS[$choice]::getGroup());
                },
                'label_attr' => ['class' => 'required'],
                'attr' => [
                    'required' => 'required',
                    'data-controller' => 'select accordion-label file-select help',
                    'data-help-texts-value' => json_encode(
                        array_combine(
                            $commands,
                            array_map(
                                fn (string $command) => [
                                    'target' => CommandManagerInterface::COMMANDS[$command]::getTargetHelper(),
                                    'value' => CommandManagerInterface::COMMANDS[$command]::getValueHelper(),
                                ],
                                $commands,
                            )
                        )
                    ),
                ],
            ])
            ->add('target', TextType::class, [
                'label' => 'Target',
                'required' => false,
                'help' => '<span class="target-help"></span>',
                'help_html' => true,
            ])
            ->add('value', TextType::class, [
                'label' => 'Value',
                'required' => false,
                'attr' => [
                    'class' => 'command-value',
                ],
                'help' => '<span class="value-help"></span>',
                'help_html' => true,
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
