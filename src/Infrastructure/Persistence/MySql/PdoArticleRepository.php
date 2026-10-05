<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\MySql;

use App\Domain\Model\Article;
use App\Domain\Model\Category;
use App\Domain\Repository\ArticleRepository;
use DateTimeImmutable;
use DateTimeZone;
use PDO;

final readonly class PdoArticleRepository implements ArticleRepository
{
    public function __construct(private PDO $connection) {}

    public function findById(int $id): ?Article
    {
        $statement = $this->connection->prepare(<<<'SQL'
            SELECT id, image_path, title, description, content, view_count, published_at
            FROM articles
            WHERE id = :id
            SQL);
        $statement->bindValue('id', $id, PDO::PARAM_INT);
        $statement->execute();

        $row = $statement->fetch();

        if ($row === false) {
            return null;
        }

        return new Article(
            id: (int) $row['id'],
            imagePath: (string) $row['image_path'],
            title: (string) $row['title'],
            description: (string) $row['description'],
            content: (string) $row['content'],
            publishedAt: new DateTimeImmutable(
                (string) $row['published_at'],
                new DateTimeZone('UTC'),
            ),
            viewCount: (int) $row['view_count'],
            categories: $this->findCategories($id),
        );
    }

    /**
     * @return list<Category>
     */
    private function findCategories(int $articleId): array
    {
        $statement = $this->connection->prepare(<<<'SQL'
            SELECT category.id, category.name, category.description
            FROM categories AS category
            INNER JOIN article_category AS relation ON relation.category_id = category.id
            WHERE relation.article_id = :article_id
            ORDER BY category.id
            SQL);
        $statement->bindValue('article_id', $articleId, PDO::PARAM_INT);
        $statement->execute();

        $categories = [];

        while (($row = $statement->fetch()) !== false) {
            $categories[] = new Category(
                id: (int) $row['id'],
                name: (string) $row['name'],
                description: (string) $row['description'],
            );
        }

        return $categories;
    }
}
