<?php

/**
 * This file is part of the SICOPE Model package.
 *
 * @package     sicope-model
 * @license     LICENSE
 * @author      Tien Xuan Vo <tien.xuan.vo@gmail.com>
 * @link        https://github.com/sicope-model/sicope-model
 */

namespace App\Field\Configurator;

use App\Service\BrowserFormatter;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldConfiguratorInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FieldDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use Tienvx\Bundle\MbtBundle\Model\Task\BrowserInterface;

final class BrowserChoiceConfigurator implements FieldConfiguratorInterface
{
    public function __construct(private BrowserFormatter $formatter)
    {
    }

    public function supports(FieldDto $field, EntityDto $entityDto): bool
    {
        return ChoiceField::class === $field->getFieldFqcn();
    }

    public function configure(FieldDto $field, EntityDto $entityDto, AdminContext $context): void
    {
        $fieldValue = $field->getValue();
        if ($fieldValue instanceof BrowserInterface) {
            $field->setFormattedValue($this->formatter->format($fieldValue->getName(), $fieldValue->getVersion()));
        }
    }

    public static function getDefaultPriority(): int
    {
        return -1;
    }
}
