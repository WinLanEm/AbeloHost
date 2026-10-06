<?php

declare(strict_types=1);

namespace Tests\Feature\Http;

use PHPUnit\Framework\Attributes\CoversNothing;
use Tests\Support\FeatureTestCase;

#[CoversNothing]
final class HomePageTest extends FeatureTestCase
{
    public function testItShowsTheThreeLatestArticlesForNonEmptyCategories(): void
    {
        $this->fixtures->category(1, 'PHP');
        $this->fixtures->category(2, 'Empty category');

        $this->fixtures->article(1, publishedAt: '2026-01-01 00:00:00', title: 'Oldest article');
        $this->fixtures->article(2, publishedAt: '2026-01-02 00:00:00', title: 'Second article');
        $this->fixtures->article(3, publishedAt: '2026-01-03 00:00:00', title: 'Third article');
        $this->fixtures->article(4, publishedAt: '2026-01-04 00:00:00', title: 'Latest article');

        for ($articleId = 1; $articleId <= 4; ++$articleId) {
            $this->fixtures->attachArticleToCategory($articleId, 1);
        }

        $response = $this->get('/');
        $body = $response->body();

        self::assertSame(200, $response->statusCode());
        self::assertSame('text/html; charset=UTF-8', $response->headers()['Content-Type']);
        self::assertStringContainsString('PHP', $body);
        self::assertStringContainsString('Latest article', $body);
        self::assertStringContainsString('Third article', $body);
        self::assertStringContainsString('Second article', $body);
        self::assertStringNotContainsString('Oldest article', $body);
        self::assertStringNotContainsString('Empty category', $body);

        $latestPosition = strpos($body, 'Latest article');
        $thirdPosition = strpos($body, 'Third article');
        $secondPosition = strpos($body, 'Second article');

        self::assertIsInt($latestPosition);
        self::assertIsInt($thirdPosition);
        self::assertIsInt($secondPosition);
        self::assertTrue($latestPosition < $thirdPosition);
        self::assertTrue($thirdPosition < $secondPosition);
    }
}
