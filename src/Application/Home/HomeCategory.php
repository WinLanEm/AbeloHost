<?php

declare(strict_types=1);

namespace App\Application\Home;

use App\Domain\Model\ArticleSummary;
use App\Domain\Model\Category;

final readonly class HomeCategory
{
    /**
     * @param list<ArticleSummary> $articles
     */
    public function __construct(
        private Category $category,
        private array $articles,
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
}
