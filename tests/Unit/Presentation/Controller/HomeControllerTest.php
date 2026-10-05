<?php

declare(strict_types=1);

namespace Tests\Unit\Presentation\Controller;

use App\Application\View\ViewRenderer;
use App\Presentation\Controller\HomeController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(HomeController::class)]
final class HomeControllerTest extends TestCase
{
    public function testItRendersTheHomePage(): void
    {
        $renderer = $this->createMock(ViewRenderer::class);
        $renderer
            ->expects(self::once())
            ->method('render')
            ->with('home.tpl', ['pageTitle' => 'PHP Blog'])
            ->willReturn('<h1>PHP Blog</h1>');

        $controller = new HomeController($renderer);

        self::assertSame('<h1>PHP Blog</h1>', $controller());
    }
}
