<?php

declare(strict_types=1);

namespace Tests\Unit\Presentation\Controller;

use App\Application\Home\GetHomePage;
use App\Application\View\ViewRenderer;
use App\Domain\Repository\ArticleRepository;
use App\Domain\Repository\CategoryRepository;
use App\Presentation\Controller\HomeController;
use App\Presentation\Http\Request;
use App\Presentation\View\BlogViewData;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(HomeController::class)]
final class HomeControllerTest extends TestCase
{
    public function testItRendersTheHomePage(): void
    {
        $categoryRepository = $this->createMock(CategoryRepository::class);
        $categoryRepository
            ->expects(self::once())
            ->method('findWithArticles')
            ->willReturn([]);

        $articleRepository = $this->createMock(ArticleRepository::class);
        $articleRepository
            ->expects(self::never())
            ->method('findLatestGroupedByCategory');

        $renderer = $this->createMock(ViewRenderer::class);
        $renderer
            ->expects(self::once())
            ->method('render')
            ->with('home.tpl', [
                'pageTitle' => 'PHP Blog',
                'categories' => [],
            ])
            ->willReturn('<h1>PHP Blog</h1>');

        $controller = new HomeController(
            getHomePage: new GetHomePage(
                categories: $categoryRepository,
                articles: $articleRepository,
            ),
            renderer: $renderer,
            viewData: new BlogViewData(),
        );
        $response = $controller(new Request('GET', '/'));

        self::assertSame(200, $response->statusCode());
        self::assertSame('<h1>PHP Blog</h1>', $response->body());
        self::assertSame('text/html; charset=UTF-8', $response->headers()['Content-Type']);
    }
}
