<?php

declare(strict_types=1);

namespace Tests\Feature\Http;

use PHPUnit\Framework\Attributes\CoversNothing;
use Tests\Support\FeatureTestCase;

#[CoversNothing]
final class ArticlePageTest extends FeatureTestCase
{
    public function testItShowsAnArticleAndItsMostSimilarArticles(): void
    {
        $this->fixtures->category(1, 'PHP');
        $this->fixtures->category(2, 'Architecture');
        $this->fixtures->category(3, 'Databases');

        $this->fixtures->article(1, viewCount: 5, title: 'Current article');
        $this->fixtures->article(2, publishedAt: '2026-01-02 00:00:00', title: 'Best match');
        $this->fixtures->article(3, publishedAt: '2026-01-03 00:00:00', title: 'Other match');
        $this->fixtures->article(4, publishedAt: '2026-01-04 00:00:00', title: 'Unrelated article');

        $this->fixtures->attachArticleToCategory(1, 1);
        $this->fixtures->attachArticleToCategory(1, 2);
        $this->fixtures->attachArticleToCategory(2, 1);
        $this->fixtures->attachArticleToCategory(2, 2);
        $this->fixtures->attachArticleToCategory(3, 1);
        $this->fixtures->attachArticleToCategory(4, 3);

        $response = $this->get('/articles/1');
        $body = $response->body();

        self::assertSame(200, $response->statusCode());
        self::assertStringContainsString('Current article', $body);
        self::assertStringContainsString('Test article content', $body);
        self::assertStringContainsString('PHP', $body);
        self::assertStringContainsString('Architecture', $body);
        self::assertStringContainsString('6 просмотров', $body);
        self::assertStringContainsString('Best match', $body);
        self::assertStringContainsString('Other match', $body);
        self::assertStringNotContainsString('Unrelated article', $body);

        $bestMatchPosition = strpos($body, 'Best match');
        $otherMatchPosition = strpos($body, 'Other match');

        self::assertIsInt($bestMatchPosition);
        self::assertIsInt($otherMatchPosition);
        self::assertTrue($bestMatchPosition < $otherMatchPosition);

        $statement = $this->connection->query('SELECT view_count FROM articles WHERE id = 1');
        self::assertNotFalse($statement);
        self::assertSame(6, (int) $statement->fetchColumn());
    }

    public function testItReturnsNotFoundForAnUnknownArticle(): void
    {
        $response = $this->get('/articles/999');

        self::assertSame(404, $response->statusCode());
        self::assertStringContainsString('Article not found', $response->body());
    }
}
