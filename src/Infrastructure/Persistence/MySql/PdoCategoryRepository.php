<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\MySql;

use App\Domain\Model\Category;
use App\Domain\Repository\CategoryRepository;
use PDO;
use RuntimeException;

final readonly class PdoCategoryRepository implements CategoryRepository
{
    public function __construct(private PDO $connection) {}

    public function findById(int $id): ?Category
    {
        $statement = $this->connection->prepare(<<<'SQL'
            SELECT id, name, description
            FROM categories
            WHERE id = :id
            SQL);
        $statement->bindValue('id', $id, PDO::PARAM_INT);
        $statement->execute();

        $row = $statement->fetch();

        if ($row === false) {
            return null;
        }

        return $this->hydrate($row);
    }

    public function findWithArticles(): array
    {
        $statement = $this->connection->query(<<<'SQL'
            SELECT category.id, category.name, category.description
            FROM categories AS category
            WHERE EXISTS (
                SELECT 1
                FROM article_category AS relation
                WHERE relation.category_id = category.id
            )
            ORDER BY category.name, category.id
            SQL);

        if ($statement === false) {
            throw new RuntimeException('Could not fetch categories with articles.');
        }

        $categories = [];

        while (($row = $statement->fetch()) !== false) {
            $categories[] = $this->hydrate($row);
        }

        return $categories;
    }

    /**
     * @param array<string, mixed> $row
     */
    private function hydrate(array $row): Category
    {
        return new Category(
            id: (int) $row['id'],
            name: (string) $row['name'],
            description: (string) $row['description'],
        );
    }
}
