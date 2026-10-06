<?php

declare(strict_types=1);

namespace Tests\Feature\Http;

use PHPUnit\Framework\Attributes\CoversNothing;
use Tests\Support\FeatureTestCase;

#[CoversNothing]
final class CategoryPageTest extends FeatureTestCase
{
    public function testItSortsArticlesByViewCount(): void
    {
        $this->fixtures->category(1, 'PHP');
        $this->fixtures->article(1, viewCount: 10, title: 'Least viewed');
        $this->fixtures->article(2, viewCount: 30, title: 'Most viewed');
        $this->fixtures->article(3, viewCount: 20, title: 'Middle viewed');

        for ($articleId = 1; $articleId <= 3; ++$articleId) {
            $this->fixtures->attachArticleToCategory($articleId, 1);
        }

        $response = $this->get('/categories/1?sort=views');
        $body = $response->body();

        self::assertSame(200, $response->statusCode());
        self::assertStringContainsString('Статей: 3', $body);
        self::assertStringContainsString('<strong aria-current="true">По просмотрам</strong>', $body);

        $mostViewedPosition = strpos($body, 'Most viewed');
        $middleViewedPosition = strpos($body, 'Middle viewed');
        $leastViewedPosition = strpos($body, 'Least viewed');

        self::assertIsInt($mostViewedPosition);
        self::assertIsInt($middleViewedPosition);
        self::assertIsInt($leastViewedPosition);
        self::assertTrue($mostViewedPosition < $middleViewedPosition);
        self::assertTrue($middleViewedPosition < $leastViewedPosition);
    }

    public function testItPaginatesArticlesOrderedByDate(): void
    {
        $this->fixtures->category(1, 'PHP');
        $this->fixtures->article(1, publishedAt: '2026-01-01 00:00:00', title: 'Oldest article');
        $this->fixtures->article(2, publishedAt: '2026-01-02 00:00:00', title: 'Second article');
        $this->fixtures->article(3, publishedAt: '2026-01-03 00:00:00', title: 'Third article');
        $this->fixtures->article(4, publishedAt: '2026-01-04 00:00:00', title: 'Latest article');

        for ($articleId = 1; $articleId <= 4; ++$articleId) {
            $this->fixtures->attachArticleToCategory($articleId, 1);
        }

        $response = $this->get('/categories/1?page=2');
        $body = $response->body();

        self::assertSame(200, $response->statusCode());
        self::assertStringContainsString('Статей: 4', $body);
        self::assertStringContainsString('Oldest article', $body);
        self::assertStringNotContainsString('Latest article', $body);
        self::assertStringContainsString('<strong aria-current="page">2</strong>', $body);
    }

    public function testItReturnsNotFoundForAnUnknownCategory(): void
    {
        $response = $this->get('/categories/999');

        self::assertSame(404, $response->statusCode());
        self::assertStringContainsString('Category not found', $response->body());
    }
}
