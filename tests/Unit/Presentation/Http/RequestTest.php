<?php

declare(strict_types=1);

namespace Tests\Unit\Presentation\Http;

use App\Presentation\Http\Request;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Request::class)]
final class RequestTest extends TestCase
{
    /** @var array<string, mixed> */
    private array $server;

    /** @var array<string, mixed> */
    private array $queryParameters;

    protected function setUp(): void
    {
        $this->server = $_SERVER;
        $this->queryParameters = $_GET;
    }

    protected function tearDown(): void
    {
        $_SERVER = $this->server;
        $_GET = $this->queryParameters;
    }

    public function testItBuildsARequestFromGlobals(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'get';
        $_SERVER['REQUEST_URI'] = '/categories/7?sort=views&page=2';
        $_GET = [
            'sort' => 'views',
            'page' => '2',
        ];

        $request = Request::fromGlobals();

        self::assertSame('GET', $request->method());
        self::assertSame('/categories/7', $request->path());
        self::assertSame('views', $request->stringQuery('sort', 'date'));
        self::assertSame(2, $request->positiveIntegerQuery('page', 1));
    }

    public function testItUsesDefaultsForInvalidQueryParameters(): void
    {
        $request = new Request('GET', '/', [
            'sort' => ['views'],
            'page' => '0',
        ]);

        self::assertSame('date', $request->stringQuery('sort', 'date'));
        self::assertSame(1, $request->positiveIntegerQuery('page', 1));
    }

    public function testItRejectsAPathWithoutALeadingSlash(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Request('GET', 'articles/1');
    }
}
