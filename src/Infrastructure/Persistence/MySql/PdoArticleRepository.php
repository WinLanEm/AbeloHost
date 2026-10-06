<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\MySql;

use App\Domain\Model\Article;
use App\Domain\Model\ArticleSummary;
use App\Domain\Model\Category;
use App\Domain\Repository\ArticleOrder;
use App\Domain\Repository\ArticleRepository;
use DateTimeImmutable;
use DateTimeZone;
use InvalidArgumentException;
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

    public function findLatestGroupedByCategory(int $limitPerCategory): array
    {
        if ($limitPerCategory < 1) {
            throw new InvalidArgumentException('Article limit per category must be positive.');
        }

        $statement = $this->connection->prepare(<<<'SQL'
            SELECT
                ranked.category_id,
                ranked.id,
                ranked.image_path,
                ranked.title,
                ranked.description,
                ranked.view_count,
                ranked.published_at
            FROM (
                SELECT
                    relation.category_id,
                    article.id,
                    article.image_path,
                    article.title,
                    article.description,
                    article.view_count,
                    article.published_at,
                    ROW_NUMBER() OVER (
                        PARTITION BY relation.category_id
                        ORDER BY article.published_at DESC, article.id DESC
                    ) AS category_position
                FROM article_category AS relation
                INNER JOIN articles AS article ON article.id = relation.article_id
            ) AS ranked
            WHERE ranked.category_position <= :limit_per_category
            ORDER BY ranked.category_id, ranked.published_at DESC, ranked.id DESC
            SQL);
        $statement->bindValue('limit_per_category', $limitPerCategory, PDO::PARAM_INT);
        $statement->execute();

        $articlesByCategory = [];

        while (($row = $statement->fetch()) !== false) {
            $categoryId = (int) $row['category_id'];
            $articlesByCategory[$categoryId] ??= [];
            $articlesByCategory[$categoryId][] = $this->hydrateArticleSummary($row);
        }

        return $articlesByCategory;
    }

    public function findByCategory(
        int $categoryId,
        ArticleOrder $order,
        int $limit,
        int $offset,
    ): array {
        if ($limit < 1) {
            throw new InvalidArgumentException('Article limit must be positive.');
        }

        if ($offset < 0) {
            throw new InvalidArgumentException('Article offset must not be negative.');
        }

        $orderBy = match ($order) {
            ArticleOrder::PublishedAt => 'article.published_at DESC, article.id DESC',
            ArticleOrder::ViewCount => 'article.view_count DESC, article.published_at DESC, article.id DESC',
        };

        $statement = $this->connection->prepare(sprintf(<<<'SQL'
            SELECT
                article.id,
                article.image_path,
                article.title,
                article.description,
                article.view_count,
                article.published_at
            FROM articles AS article
            INNER JOIN article_category AS relation ON relation.article_id = article.id
            WHERE relation.category_id = :category_id
            ORDER BY %s
            LIMIT :article_limit OFFSET :article_offset
            SQL, $orderBy));
        $statement->bindValue('category_id', $categoryId, PDO::PARAM_INT);
        $statement->bindValue('article_limit', $limit, PDO::PARAM_INT);
        $statement->bindValue('article_offset', $offset, PDO::PARAM_INT);
        $statement->execute();

        return $this->hydrateArticleSummaries($statement);
    }

    public function countByCategory(int $categoryId): int
    {
        $statement = $this->connection->prepare(<<<'SQL'
            SELECT COUNT(*)
            FROM article_category
            WHERE category_id = :category_id
            SQL);
        $statement->bindValue('category_id', $categoryId, PDO::PARAM_INT);
        $statement->execute();

        return (int) $statement->fetchColumn();
    }

    public function incrementViewCount(int $articleId): bool
    {
        $statement = $this->connection->prepare(<<<'SQL'
            UPDATE articles
            SET view_count = view_count + 1
            WHERE id = :article_id
            SQL);
        $statement->bindValue('article_id', $articleId, PDO::PARAM_INT);
        $statement->execute();

        return $statement->rowCount() === 1;
    }

    public function findSimilar(int $articleId, int $limit): array
    {
        if ($limit < 1) {
            throw new InvalidArgumentException('Similar article limit must be positive.');
        }

        $statement = $this->connection->prepare(<<<'SQL'
            SELECT
                candidate.id,
                candidate.image_path,
                candidate.title,
                candidate.description,
                candidate.view_count,
                candidate.published_at,
                COUNT(DISTINCT candidate_relation.category_id) AS shared_category_count
            FROM articles AS candidate
            INNER JOIN article_category AS candidate_relation
                ON candidate_relation.article_id = candidate.id
            INNER JOIN article_category AS current_relation
                ON current_relation.category_id = candidate_relation.category_id
                AND current_relation.article_id = :current_article_id
            WHERE candidate.id <> :excluded_article_id
            GROUP BY
                candidate.id,
                candidate.image_path,
                candidate.title,
                candidate.description,
                candidate.view_count,
                candidate.published_at
            ORDER BY shared_category_count DESC, candidate.published_at DESC, candidate.id DESC
            LIMIT :article_limit
            SQL);
        $statement->bindValue('current_article_id', $articleId, PDO::PARAM_INT);
        $statement->bindValue('excluded_article_id', $articleId, PDO::PARAM_INT);
        $statement->bindValue('article_limit', $limit, PDO::PARAM_INT);
        $statement->execute();

        return $this->hydrateArticleSummaries($statement);
    }

    /**
     * @return list<ArticleSummary>
     */
    private function hydrateArticleSummaries(\PDOStatement $statement): array
    {
        $articles = [];

        while (($row = $statement->fetch()) !== false) {
            $articles[] = $this->hydrateArticleSummary($row);
        }

        return $articles;
    }

    /**
     * @param array<string, mixed> $row
     */
    private function hydrateArticleSummary(array $row): ArticleSummary
    {
        return new ArticleSummary(
            id: (int) $row['id'],
            imagePath: (string) $row['image_path'],
            title: (string) $row['title'],
            description: (string) $row['description'],
            publishedAt: new DateTimeImmutable(
                (string) $row['published_at'],
                new DateTimeZone('UTC'),
            ),
            viewCount: (int) $row['view_count'],
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
