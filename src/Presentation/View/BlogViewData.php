<?php

declare(strict_types=1);

namespace App\Presentation\View;

use App\Application\Article\ArticlePage;
use App\Application\Category\CategoryPage;
use App\Application\Home\HomeCategory;
use App\Domain\Model\ArticleSummary;

final class BlogViewData
{
    /**
     * @param list<HomeCategory> $homeCategories
     *
     * @return array{
     *     pageTitle: string,
     *     categories: list<array{
     *         id: int,
     *         name: string,
     *         description: string,
     *         articles: list<array{
     *             id: int,
     *             imagePath: string,
     *             title: string,
     *             description: string,
     *             publishedAt: string,
     *             publishedAtLabel: string,
     *             viewCount: int
     *         }>
     *     }>
     * }
     */
    public function home(array $homeCategories): array
    {
        $categories = [];

        foreach ($homeCategories as $homeCategory) {
            $category = $homeCategory->category();
            $articles = [];

            foreach ($homeCategory->articles() as $article) {
                $articles[] = $this->articleCard($article);
            }

            $categories[] = [
                'id' => $category->id(),
                'name' => $category->name(),
                'description' => $category->description(),
                'articles' => $articles,
            ];
        }

        return [
            'pageTitle' => 'PHP Blog',
            'categories' => $categories,
        ];
    }

    /**
     * @return array{
     *     pageTitle: string,
     *     category: array{id: int, name: string, description: string},
     *     articles: list<array{
     *         id: int,
     *         imagePath: string,
     *         title: string,
     *         description: string,
     *         publishedAt: string,
     *         publishedAtLabel: string,
     *         viewCount: int
     *     }>,
     *     currentSort: string,
     *     totalArticles: int,
     *     pages: list<
     *         array{type: 'page', number: int, url: string, isCurrent: bool}
     *         |array{type: 'ellipsis'}
     *     >
     * }
     */
    public function category(CategoryPage $categoryPage): array
    {
        $category = $categoryPage->category();
        $articles = [];

        foreach ($categoryPage->articles() as $article) {
            $articles[] = $this->articleCard($article);
        }

        return [
            'pageTitle' => $category->name(),
            'category' => [
                'id' => $category->id(),
                'name' => $category->name(),
                'description' => $category->description(),
            ],
            'articles' => $articles,
            'currentSort' => $categoryPage->order()->value,
            'totalArticles' => $categoryPage->totalArticles(),
            'pages' => $this->pagination($categoryPage),
        ];
    }

    /**
     * @return list<
     *     array{type: 'page', number: int, url: string, isCurrent: bool}
     *     |array{type: 'ellipsis'}
     * >
     */
    private function pagination(CategoryPage $categoryPage): array
    {
        $currentPage = $categoryPage->currentPage();
        $totalPages = $categoryPage->totalPages();

        /** @var array<int, true> $visiblePages */
        $visiblePages = [
            1 => true,
            $totalPages => true,
        ];

        $windowStart = max(1, $currentPage - 2);
        $windowEnd = min($totalPages, $currentPage + 2);

        for ($pageNumber = $windowStart; $pageNumber <= $windowEnd; ++$pageNumber) {
            $visiblePages[$pageNumber] = true;
        }

        $pageNumbers = array_keys($visiblePages);
        sort($pageNumbers, SORT_NUMERIC);

        /** @var list<int|null> $sequence */
        $sequence = [];
        $previousPage = null;

        foreach ($pageNumbers as $pageNumber) {
            if ($previousPage !== null) {
                $gap = $pageNumber - $previousPage;

                if ($gap === 2) {
                    $sequence[] = $previousPage + 1;
                } elseif ($gap > 2) {
                    $sequence[] = null;
                }
            }

            $sequence[] = $pageNumber;
            $previousPage = $pageNumber;
        }

        $items = [];
        $category = $categoryPage->category();

        foreach ($sequence as $pageNumber) {
            if ($pageNumber === null) {
                $items[] = ['type' => 'ellipsis'];
                continue;
            }

            $items[] = [
                'type' => 'page',
                'number' => $pageNumber,
                'url' => sprintf(
                    '/categories/%d?sort=%s&page=%d',
                    $category->id(),
                    $categoryPage->order()->value,
                    $pageNumber,
                ),
                'isCurrent' => $pageNumber === $currentPage,
            ];
        }

        return $items;
    }

    /**
     * @return array{
     *     pageTitle: string,
     *     article: array{
     *         imagePath: string,
     *         title: string,
     *         description: string,
     *         content: string,
     *         publishedAt: string,
     *         publishedAtLabel: string,
     *         viewCount: int,
     *         categories: list<array{id: int, name: string}>
     *     },
     *     similarArticles: list<array{
     *         id: int,
     *         imagePath: string,
     *         title: string,
     *         description: string,
     *         publishedAt: string,
     *         publishedAtLabel: string,
     *         viewCount: int
     *     }>
     * }
     */
    public function article(ArticlePage $articlePage): array
    {
        $article = $articlePage->article();
        $categories = [];

        foreach ($article->categories() as $category) {
            $categories[] = [
                'id' => $category->id(),
                'name' => $category->name(),
            ];
        }

        $similarArticles = [];

        foreach ($articlePage->similarArticles() as $similarArticle) {
            $similarArticles[] = $this->articleCard($similarArticle);
        }

        return [
            'pageTitle' => $article->title(),
            'article' => [
                'imagePath' => $article->imagePath(),
                'title' => $article->title(),
                'description' => $article->description(),
                'content' => $article->content(),
                'publishedAt' => $article->publishedAt()->format('Y-m-d'),
                'publishedAtLabel' => $article->publishedAt()->format('d.m.Y'),
                'viewCount' => $article->viewCount(),
                'categories' => $categories,
            ],
            'similarArticles' => $similarArticles,
        ];
    }

    /**
     * @return array{
     *     id: int,
     *     imagePath: string,
     *     title: string,
     *     description: string,
     *     publishedAt: string,
     *     publishedAtLabel: string,
     *     viewCount: int
     * }
     */
    private function articleCard(ArticleSummary $article): array
    {
        return [
            'id' => $article->id(),
            'imagePath' => $article->imagePath(),
            'title' => $article->title(),
            'description' => $article->description(),
            'publishedAt' => $article->publishedAt()->format('Y-m-d'),
            'publishedAtLabel' => $article->publishedAt()->format('d.m.Y'),
            'viewCount' => $article->viewCount(),
        ];
    }
}
