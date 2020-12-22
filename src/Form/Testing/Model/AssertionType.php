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

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Tienvx\Bundle\MbtBundle\Model\Model\CommandInterface;

class AssertionType extends CommandType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('command', ChoiceType::class, [
                'label' => 'command_assertion',
                'choices' => array_combine(CommandInterface::ALL_ASSERTIONS, CommandInterface::ALL_ASSERTIONS),
            ])
        ;
        parent::buildForm($builder, $options);
    }
}
