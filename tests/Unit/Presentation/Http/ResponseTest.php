<?php

declare(strict_types=1);

namespace Tests\Unit\Presentation\Http;

use App\Presentation\Http\Response;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Response::class)]
final class ResponseTest extends TestCase
{
    public function testItCreatesAnHtmlResponse(): void
    {
        $response = Response::html('<h1>Created</h1>', 201);

        self::assertSame(201, $response->statusCode());
        self::assertSame('<h1>Created</h1>', $response->body());
        self::assertSame(
            ['Content-Type' => 'text/html; charset=UTF-8'],
            $response->headers(),
        );
    }

    public function testItCreatesAPlainTextResponse(): void
    {
        $response = Response::text('Not Found', 404);

        self::assertSame(404, $response->statusCode());
        self::assertSame('Not Found', $response->body());
        self::assertSame(
            ['Content-Type' => 'text/plain; charset=UTF-8'],
            $response->headers(),
        );
    }

    public function testItRejectsAnInvalidStatusCode(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Response(statusCode: 99);
    }
}
