<?php

declare(strict_types=1);

use App\Presentation\Http\HttpApplication;
use App\Presentation\Http\Request;

$autoloadPath = dirname(__DIR__) . '/vendor/autoload.php';

if (!is_file($autoloadPath)) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'Composer dependencies are not installed. Run composer install.';

    return;
}

require_once $autoloadPath;

try {
    /** @var Closure(?PDO=): HttpApplication $createApplication */
    $createApplication = require dirname(__DIR__) . '/bootstrap/application.php';
    $application = $createApplication();

    $application->handle(Request::fromGlobals())->send();
} catch (Throwable $exception) {
    error_log((string) $exception);

    if (!headers_sent()) {
        http_response_code(500);
        header('Content-Type: text/plain; charset=UTF-8');
    }

    echo 'Internal Server Error';
}
