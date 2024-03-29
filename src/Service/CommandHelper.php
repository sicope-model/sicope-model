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

class CommandHelper
{
    public function verifyCommand($command): bool
    {
        $windows = 0 === strpos(\PHP_OS, 'WIN');
        $test = $windows ? 'where' : 'command -v';

        return is_executable(trim(shell_exec("$test $command")));
    }
}
