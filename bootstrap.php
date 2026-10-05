<?php

declare(strict_types=1);

use App\Infrastructure\View\SmartyRenderer;
use App\Presentation\Controller\HomeController;

$autoloadPath = __DIR__ . '/vendor/autoload.php';

if (!is_file($autoloadPath)) {
    throw new RuntimeException('Composer dependencies are not installed. Run composer install.');
}

require $autoloadPath;

$renderer = new SmartyRenderer(
    templateDirectory: __DIR__ . '/templates',
    compileDirectory: __DIR__ . '/var/cache/smarty/templates_c',
);

return new HomeController($renderer);
