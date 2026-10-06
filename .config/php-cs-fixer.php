<?php

declare(strict_types=1);

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$projectDirectory = dirname(__DIR__);
$finder = Finder::create()
    ->files()
    ->in($projectDirectory)
    ->exclude([
        'var',
        'vendor',
    ])
    ->name('*.php');

return (new Config())
    ->setCacheFile($projectDirectory . '/var/cache/php-cs-fixer.cache')
    ->setRules([
        '@PER-CS2.0' => true,
        'array_syntax' => ['syntax' => 'short'],
        'ordered_imports' => ['sort_algorithm' => 'alpha'],
        'single_quote' => true,
        'trailing_comma_in_multiline' => [
            'elements' => [
                'arguments',
                'arrays',
                'match',
                'parameters',
            ],
        ],
    ])
    ->setFinder($finder);
