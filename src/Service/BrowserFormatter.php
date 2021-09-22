<?php

/**
 * This file is part of the SICOPE Model package.
 *
 * @package     sicope-model
 * @license     LICENSE
 * @author      Tien Xuan Vo <tien.xuan.vo@gmail.com>
 * @link        https://github.com/sicope-model/sicope-model
 */

namespace App\Service;

use Symfony\Contracts\Translation\TranslatorInterface;

class BrowserFormatter
{
    public function __construct(private TranslatorInterface $translator)
    {
    }

    public function format(string $name, string $version): string
    {
        return sprintf('%s %s', $this->translator->trans($name), $version);
    }
}
