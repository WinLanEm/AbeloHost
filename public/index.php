<?php

declare(strict_types=1);

try {
    $homeController = require dirname(__DIR__) . '/bootstrap.php';

    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

    if ($method !== 'GET' || $path !== '/') {
        http_response_code(404);
        header('Content-Type: text/plain; charset=UTF-8');
        echo 'Not Found';
        return;
    }

    header('Content-Type: text/html; charset=UTF-8');
    echo $homeController();
} catch (Throwable $exception) {
    error_log((string) $exception);

    http_response_code(500);
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'Internal Server Error';
}
