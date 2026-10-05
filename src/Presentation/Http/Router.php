<?php

declare(strict_types=1);

namespace App\Presentation\Http;

use Closure;

final class Router
{
    /**
     * @var list<array{
     *     method: string,
     *     path: string,
     *     handler: Closure(Request, array<string, string>): Response
     * }>
     */
    private array $routes = [];

    /**
     * @param callable(Request, array<string, string>): Response $handler
     */
    public function get(string $path, callable $handler): void
    {
        $this->routes[] = [
            'method' => 'GET',
            'path' => $path,
            'handler' => Closure::fromCallable($handler),
        ];
    }

    public function dispatch(Request $request): Response
    {
        foreach ($this->routes as $route) {
            if ($route['method'] !== $request->method()) {
                continue;
            }

            $parameters = $this->match($route['path'], $request->path());

            if ($parameters !== null) {
                return ($route['handler'])($request, $parameters);
            }
        }

        return Response::text('Not Found', 404);
    }

    /**
     * @return array<string, string>|null
     */
    private function match(string $routePath, string $requestPath): ?array
    {
        $routeSegments = explode('/', trim($routePath, '/'));
        $requestSegments = explode('/', trim($requestPath, '/'));

        if (count($routeSegments) !== count($requestSegments)) {
            return null;
        }

        $parameters = [];

        foreach ($routeSegments as $index => $routeSegment) {
            $requestSegment = $requestSegments[$index];

            if (preg_match('/^\{([a-zA-Z_][a-zA-Z0-9_]*)\}$/', $routeSegment, $matches) === 1) {
                $parameters[$matches[1]] = rawurldecode($requestSegment);
                continue;
            }

            if ($routeSegment !== $requestSegment) {
                return null;
            }
        }

        return $parameters;
    }
}
