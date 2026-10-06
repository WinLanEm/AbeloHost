<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Model\Article;
use App\Domain\Model\ArticleSummary;

interface ArticleRepository
{
    public function findById(int $id): ?Article;

    /**
     * @return array<int, list<ArticleSummary>> Articles indexed by category id.
     */
    public function findLatestGroupedByCategory(int $limitPerCategory): array;

    /**
     * @return list<ArticleSummary>
     */
    public function findByCategory(
        int $categoryId,
        ArticleOrder $order,
        int $limit,
        int $offset,
    ): array;

    public function countByCategory(int $categoryId): int;

    public function incrementViewCount(int $articleId): bool;

    /**
     * @return list<ArticleSummary>
     */
    public function findSimilar(int $articleId, int $limit): array;
}
