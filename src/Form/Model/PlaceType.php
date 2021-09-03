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
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tienvx\Bundle\MbtBundle\ValueObject\Model\Place;

class PlaceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('label', TextType::class, [
                'label' => 'Label',
                // Workaround for https://github.com/EasyCorp/EasyAdminBundle/issues/1124
                'label_attr' => ['class' => 'required'],
                'attr' => [
                    'required' => true,
                    'data-controller' => 'place-label',
                ],
            ])
            ->add('commands', CollectionType::class, [
                'label' => 'Commands',
                'entry_type' => CommandType::class,
                'entry_options' => [
                    'label' => false,
                ],
                'allow_add' => true,
                'allow_delete' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Place::class,
        ]);
    }
}
