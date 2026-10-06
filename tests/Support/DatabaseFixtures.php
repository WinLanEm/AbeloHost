<?php

declare(strict_types=1);

namespace Tests\Support;

use PDO;

final readonly class DatabaseFixtures
{
    public function __construct(private PDO $connection) {}

    public function category(
        int $id,
        string $name = 'Test category',
        string $description = 'Test category description',
    ): void {
        $statement = $this->connection->prepare(<<<'SQL'
            INSERT INTO categories (id, name, description)
            VALUES (:id, :name, :description)
            SQL);
        $statement->execute([
            'id' => $id,
            'name' => $name,
            'description' => $description,
        ]);
    }

    public function article(
        int $id,
        int $viewCount = 0,
        string $publishedAt = '2026-01-01 00:00:00',
        ?string $title = null,
    ): void {
        $statement = $this->connection->prepare(<<<'SQL'
            INSERT INTO articles (
                id,
                image_path,
                title,
                description,
                content,
                view_count,
                published_at
            ) VALUES (
                :id,
                :image_path,
                :title,
                :description,
                :content,
                :view_count,
                :published_at
            )
            SQL);
        $statement->execute([
            'id' => $id,
            'image_path' => '/images/articles/php.svg',
            'title' => $title ?? sprintf('Test article %d', $id),
            'description' => 'Test article description',
            'content' => 'Test article content',
            'view_count' => $viewCount,
            'published_at' => $publishedAt,
        ]);
    }

    public function attachArticleToCategory(int $articleId, int $categoryId): void
    {
        $statement = $this->connection->prepare(<<<'SQL'
            INSERT INTO article_category (article_id, category_id)
            VALUES (:article_id, :category_id)
            SQL);
        $statement->execute([
            'article_id' => $articleId,
            'category_id' => $categoryId,
        ]);
    }
}
