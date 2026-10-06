<?php

declare(strict_types=1);

namespace Tests\Unit\Presentation\Http;

use App\Presentation\Http\Request;
use App\Presentation\Http\Response;
use App\Presentation\Http\Router;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Router::class)]
final class RouterTest extends TestCase
{
    public function testItDispatchesAParameterizedRoute(): void
    {
        $router = new Router();
        $router->get(
            '/articles/{id}',
            static fn(Request $request, array $parameters): Response => Response::text(sprintf(
                '%s article %s',
                $request->method(),
                $parameters['id'],
            )),
        );

        $response = $router->dispatch(new Request('GET', '/articles/php%208'));

        self::assertSame(200, $response->statusCode());
        self::assertSame('GET article php 8', $response->body());
    }

    public function testItReturnsNotFoundForAnUnknownRoute(): void
    {
        $router = new Router();

        $response = $router->dispatch(new Request('GET', '/missing'));

        self::assertSame(404, $response->statusCode());
        self::assertSame('Not Found', $response->body());
    }

    public function testItDoesNotDispatchADifferentHttpMethod(): void
    {
        $router = new Router();
        $router->get(
            '/articles',
            static fn(): Response => Response::text('articles'),
        );

        $response = $router->dispatch(new Request('POST', '/articles'));

        self::assertSame(404, $response->statusCode());
    }
}
