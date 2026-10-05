<?php

declare(strict_types=1);

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\TestCase;

#[CoversNothing]
final class HomePageTest extends TestCase
{
    public function testHomePageReturnsSuccessfulResponse(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/';

        http_response_code(500);
        ob_start();

        require dirname(__DIR__, 2) . '/public/index.php';

        $body = ob_get_clean();

        self::assertSame(200, http_response_code());
        self::assertIsString($body);
        self::assertStringContainsString('<h1>PHP Blog</h1>', $body);
    }
}
