<?php

declare(strict_types=1);

use App\Infrastructure\View\SmartyRenderer;
use App\Presentation\Http\ExceptionHandler;
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
    /** @var array{debug: bool} $applicationConfig */
    $applicationConfig = require dirname(__DIR__) . '/config/application.php';
    /** @var array<class-string<Throwable>, array{statusCode: int, publicMessage: string}> $httpExceptionConfig */
    $httpExceptionConfig = require dirname(__DIR__) . '/config/http_exceptions.php';
    /** @var array{templateDirectory: string, compileDirectory: string} $viewConfig */
    $viewConfig = require dirname(__DIR__) . '/config/view.php';

    $exceptionHandler = new ExceptionHandler(
        renderer: new SmartyRenderer(
            templateDirectory: $viewConfig['templateDirectory'],
            compileDirectory: $viewConfig['compileDirectory'],
        ),
        debug: $applicationConfig['debug'],
        exceptionMappings: $httpExceptionConfig,
    );

    try {
        $router = require dirname(__DIR__) . '/bootstrap.php';

        $response = $router->dispatch(Request::fromGlobals());
    } catch (Throwable $exception) {
        $response = $exceptionHandler->handle($exception);
    }

    $response->send();
} catch (Throwable $exception) {
    error_log((string) $exception);

    if (!headers_sent()) {
        http_response_code(500);
        header('Content-Type: text/plain; charset=UTF-8');
    }

    echo 'Internal Server Error';
}
