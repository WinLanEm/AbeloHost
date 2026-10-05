<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Model\Article;

interface ArticleRepository
{
    public function findById(int $id): ?Article;
}
