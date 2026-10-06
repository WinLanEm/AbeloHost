<?php

declare(strict_types=1);

namespace Tests\Feature\Infrastructure\View;

use App\Infrastructure\View\SmartyRenderer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(SmartyRenderer::class)]
final class SmartyRendererTest extends TestCase
{
    public function testItRendersAndEscapesTemplateData(): void
    {
        $projectDirectory = dirname(__DIR__, 4);
        $compileDirectory = $projectDirectory . '/var/cache/phpunit/smarty';
        $renderer = new SmartyRenderer(
            templateDirectory: $projectDirectory . '/tests/Fixtures/templates',
            compileDirectory: $compileDirectory,
        );

        $result = $renderer->render('greeting.tpl', [
            'name' => '<PHP>',
        ]);

        self::assertSame('Hello, &lt;PHP&gt;!', trim($result));
        self::assertDirectoryExists($compileDirectory);
    }
}
