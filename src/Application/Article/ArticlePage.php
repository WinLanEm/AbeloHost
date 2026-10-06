<?php

declare(strict_types=1);

namespace App\Application\Article;

use App\Domain\Model\Article;
use App\Domain\Model\ArticleSummary;

final readonly class ArticlePage
{
    /**
     * @param list<ArticleSummary> $similarArticles
     */
    public function __construct(
        private Article $article,
        private array $similarArticles,
    ) {}

    public function article(): Article
    {
        return $this->article;
    }

    /**
     * @return list<ArticleSummary>
     */
    public function similarArticles(): array
    {
        return $this->similarArticles;
    }
}
