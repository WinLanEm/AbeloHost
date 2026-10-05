<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\MySql;

use App\Domain\Model\Category;
use App\Domain\Repository\CategoryRepository;
use PDO;

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

        return new Category(
            id: (int) $row['id'],
            name: (string) $row['name'],
            description: (string) $row['description'],
        );
    }
}
