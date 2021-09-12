<?php

/**
 * This file is part of the SICOPE Model package.
 *
 * @package     sicope-model
 * @license     LICENSE
 * @author      Tien Xuan Vo <tien.xuan.vo@gmail.com>
 * @link        https://github.com/sicope-model/sicope-model
 */

namespace App\Twig;

use EasyCorp\Bundle\EasyAdminBundle\Twig\EasyAdminTwigExtension;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tienvx\Bundle\MbtBundle\Model\Model\Revision\CommandInterface;
use Tienvx\Bundle\MbtBundle\Model\Model\Revision\PlaceInterface;
use Tienvx\Bundle\MbtBundle\Model\Model\Revision\TransitionInterface;

class TwigExtension extends EasyAdminTwigExtension
{
    public function __construct(
        ServiceLocator $serviceLocator,
        private TranslatorInterface $translator
    ) {
        parent::__construct($serviceLocator);
    }

    public function representAsString($value): string
    {
        if ($value instanceof PlaceInterface || $value instanceof TransitionInterface) {
            return $value->getLabel();
        }

        if ($value instanceof CommandInterface) {
            return $this->translator->trans($value->getCommand());
        }

        return parent::representAsString($value);
    }
}
