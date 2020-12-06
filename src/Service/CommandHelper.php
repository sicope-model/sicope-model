<?php

namespace App\Service;

class CommandHelper
{
    public function verifyCommand($command): bool
    {
        $windows = strpos(PHP_OS, 'WIN') === 0;
        $test = $windows ? 'where' : 'command -v';
        return is_executable(trim(shell_exec("$test $command")));
    }
}
