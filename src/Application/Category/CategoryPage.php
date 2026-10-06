<?php

declare(strict_types=1);

namespace App\Application\Category;

use App\Domain\Model\ArticleSummary;
use App\Domain\Model\Category;
use App\Domain\Repository\ArticleOrder;

final readonly class CategoryPage
{
    /**
     * @param list<ArticleSummary> $articles
     */
    public function __construct(
        private Category $category,
        private array $articles,
        private ArticleOrder $order,
        private int $currentPage,
        private int $totalPages,
        private int $totalArticles,
    ) {}

    public function category(): Category
    {
        return $this->category;
    }

    /**
     * @return list<ArticleSummary>
     */
    public function articles(): array
    {
        return $this->articles;
    }

    public function order(): ArticleOrder
    {
        return $this->order;
    }

    public function currentPage(): int
    {
        return $this->currentPage;
    }

    public function totalPages(): int
    {
        return $this->totalPages;
    }

    public function totalArticles(): int
    {
        return $this->totalArticles;
    }
}
