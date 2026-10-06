<?php

declare(strict_types=1);

namespace Tests\Unit\Presentation\Http;

use App\Application\Exception\ArticleNotFound;
use App\Application\View\ViewRenderer;
use App\Presentation\Http\ExceptionHandler;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RuntimeException;

#[CoversClass(ExceptionHandler::class)]
final class ExceptionHandlerTest extends TestCase
{
    protected function setUp(): void
    {
        ini_set('error_log', dirname(__DIR__, 4) . '/var/cache/phpunit/error.log');
    }

    protected function tearDown(): void
    {
        ini_restore('error_log');
    }

    public function testItMapsAnExceptionToAnHtmlResponse(): void
    {
        $renderer = $this->createMock(ViewRenderer::class);
        $renderer
            ->expects(self::once())
            ->method('render')
            ->with(
                'error.tpl',
                self::callback(static fn(mixed $data): bool => is_array($data)
                    && $data['statusCode'] === 404
                    && $data['publicMessage'] === 'Article not found'
                    && $data['debug'] === false),
            )
            ->willReturn('<h1>Article not found</h1>');

        $handler = new ExceptionHandler(
            renderer: $renderer,
            debug: false,
            exceptionMappings: [
                ArticleNotFound::class => [
                    'statusCode' => 404,
                    'publicMessage' => 'Article not found',
                ],
            ],
        );

        $response = $handler->handle(new ArticleNotFound(99));

        self::assertSame(404, $response->statusCode());
        self::assertSame('<h1>Article not found</h1>', $response->body());
    }

    public function testItHidesAnUnexpectedExceptionMessage(): void
    {
        $renderer = $this->createMock(ViewRenderer::class);
        $renderer
            ->expects(self::once())
            ->method('render')
            ->with(
                'error.tpl',
                self::callback(static fn(mixed $data): bool => is_array($data)
                    && $data['statusCode'] === 500
                    && $data['publicMessage'] === 'Internal Server Error'
                    && $data['debug'] === false),
            )
            ->willReturn('<h1>Internal Server Error</h1>');

        $handler = new ExceptionHandler(
            renderer: $renderer,
            debug: false,
            exceptionMappings: [],
        );

        $response = $handler->handle(new RuntimeException('Database password leaked'));

        self::assertSame(500, $response->statusCode());
        self::assertStringNotContainsString('password', $response->body());
    }

    public function testItFallsBackToAPlainTextResponseWhenRenderingFails(): void
    {
        $renderer = $this->createMock(ViewRenderer::class);
        $renderer
            ->expects(self::once())
            ->method('render')
            ->willThrowException(new RuntimeException('Renderer failed'));

        $handler = new ExceptionHandler(
            renderer: $renderer,
            debug: false,
            exceptionMappings: [
                ArticleNotFound::class => [
                    'statusCode' => 404,
                    'publicMessage' => 'Article not found',
                ],
            ],
        );

        $response = $handler->handle(new ArticleNotFound(99));

        self::assertSame(404, $response->statusCode());
        self::assertSame('Article not found', $response->body());
        self::assertSame('text/plain; charset=UTF-8', $response->headers()['Content-Type']);
    }
}
