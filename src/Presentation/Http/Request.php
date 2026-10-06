<?php

declare(strict_types=1);

namespace App\Presentation\Http;

use InvalidArgumentException;

final readonly class Request
{
    /**
     * @param array<string, mixed> $queryParameters
     */
    public function __construct(
        private string $method,
        private string $path,
        private array $queryParameters = [],
    ) {
        if ($method === '') {
            throw new InvalidArgumentException('HTTP method must not be empty.');
        }

        if (!str_starts_with($path, '/')) {
            throw new InvalidArgumentException('Request path must start with "/".');
        }
    }

    public static function fromGlobals(): self
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $requestUri = $_SERVER['REQUEST_URI'] ?? '/';

        $method = is_string($method) ? strtoupper($method) : 'GET';
        $requestUri = is_string($requestUri) ? $requestUri : '/';
        $path = parse_url($requestUri, PHP_URL_PATH);

        $queryParameters = [];

        foreach ($_GET as $name => $value) {
            if (is_string($name)) {
                $queryParameters[$name] = $value;
            }
        }

        return new self(
            method: $method,
            path: is_string($path) && $path !== '' ? $path : '/',
            queryParameters: $queryParameters,
        );
    }

    public function method(): string
    {
        return $this->method;
    }

    public function path(): string
    {
        return $this->path;
    }

    public function stringQuery(string $name, string $default): string
    {
        $value = $this->queryParameters[$name] ?? $default;

        return is_string($value) ? $value : $default;
    }

    public function positiveIntegerQuery(string $name, int $default): int
    {
        $value = filter_var(
            $this->queryParameters[$name] ?? $default,
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 1]],
        );

        return $value === false ? $default : $value;
    }
}
