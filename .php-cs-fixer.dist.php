<?php

$finder = (new PhpCsFixer\Finder())
    ->in('src')
    ->in('tests')
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@PSR12' => true,
        '@PHP74Migration' => true,
        '@PHP74Migration:risky' => true,
        'use_arrow_functions' => false,
        '@PHPUnit84Migration:risky' => true,
    ])
    ->setFinder($finder)
;
