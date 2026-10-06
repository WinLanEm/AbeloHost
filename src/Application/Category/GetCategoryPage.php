<?php

declare(strict_types=1);

namespace App\Application\Category;

use App\Application\Exception\CategoryNotFound;
use App\Domain\Repository\ArticleOrder;
use App\Domain\Repository\ArticleRepository;
use App\Domain\Repository\CategoryRepository;

final readonly class GetCategoryPage
{
    private const int ARTICLES_PER_PAGE = 3;

    public function __construct(
        private CategoryRepository $categories,
        private ArticleRepository $articles,
    ) {}

    public function execute(int $categoryId, ArticleOrder $order, int $page): CategoryPage
    {
        $category = $this->categories->findById($categoryId);

        if ($category === null) {
            throw new CategoryNotFound($categoryId);
        }

        $totalArticles = $this->articles->countByCategory($categoryId);
        $totalPages = max(1, (int) ceil($totalArticles / self::ARTICLES_PER_PAGE));
        $currentPage = min(max(1, $page), $totalPages);

        return new CategoryPage(
            category: $category,
            articles: $this->articles->findByCategory(
                categoryId: $categoryId,
                order: $order,
                limit: self::ARTICLES_PER_PAGE,
                offset: ($currentPage - 1) * self::ARTICLES_PER_PAGE,
            ),
            order: $order,
            currentPage: $currentPage,
            totalPages: $totalPages,
            totalArticles: $totalArticles,
        );
    }
}
