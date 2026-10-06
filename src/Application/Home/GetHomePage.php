<?php

declare(strict_types=1);

namespace App\Application\Home;

use App\Domain\Repository\ArticleRepository;
use App\Domain\Repository\CategoryRepository;

final readonly class GetHomePage
{
    private const int ARTICLES_PER_CATEGORY = 3;

    public function __construct(
        private CategoryRepository $categories,
        private ArticleRepository $articles,
    ) {}

    /**
     * @return list<HomeCategory>
     */
    public function execute(): array
    {
        $categories = $this->categories->findWithArticles();

        if ($categories === []) {
            return [];
        }

        $articlesByCategory = $this->articles->findLatestGroupedByCategory(
            limitPerCategory: self::ARTICLES_PER_CATEGORY,
        );
        $result = [];

        foreach ($categories as $category) {
            $result[] = new HomeCategory(
                category: $category,
                articles: $articlesByCategory[$category->id()] ?? [],
            );
        }

        return $result;
    }
}
