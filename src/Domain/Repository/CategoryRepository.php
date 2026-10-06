<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Model\Category;

interface CategoryRepository
{
    public function findById(int $id): ?Category;

    /**
     * @return list<Category>
     */
    public function findWithArticles(): array;
}
