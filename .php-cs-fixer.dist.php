<?php

$fileHeaderComment = <<<COMMENT
This file is part of the SICOPE Model package.

@package     sicope-model
@license     LICENSE
@author      Ramazan APAYDIN <apaydin541@gmail.com>
@link        https://github.com/appaydin/pd-admin
@author      Tien Xuan Vo <tien.xuan.vo@gmail.com>
@link        https://github.com/sicope-model/sicope-model
COMMENT;

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->exclude('config')
    ->exclude('var')
    ->exclude('public')
    ->exclude('assets')
    ->exclude('templates')
    ->exclude('.github')
    ->exclude('bin')
    ->exclude('docker')
    ->exclude('vendor')
    ->exclude('migrations')
;

$config = new PhpCsFixer\Config();

return $config
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR12' => true,
        '@Symfony' => true,
        '@Symfony:risky' => true,
        '@PHPUnit75Migration:risky' => true,
        'php_unit_dedicate_assert' => ['target' => '5.6'],
        'array_syntax' => ['syntax' => 'short'],
        'fopen_flags' => false,
        'protected_to_private' => false,
        'combine_nested_dirname' => true,
        'header_comment' => ['header' => $fileHeaderComment, 'separate' => 'both', 'comment_type' => 'PHPDoc'],
        'concat_space' => ['spacing' => 'one'],
        'class_definition' => ['single_line' => false],
    ])
    ->setFinder($finder)
    ->setCacheFile(__DIR__.'/var/cache/.php_cs.cache')
;
