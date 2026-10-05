<?php

declare(strict_types=1);

use App\Presentation\Http\Request;

try {
    $router = require dirname(__DIR__) . '/bootstrap.php';

    $response = $router->dispatch(Request::fromGlobals());
    $response->send();
} catch (Throwable $exception) {
    error_log((string) $exception);

    http_response_code(500);
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'Internal Server Error';
}
