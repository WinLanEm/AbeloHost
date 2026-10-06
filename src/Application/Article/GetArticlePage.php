<?php

declare(strict_types=1);

namespace App\Application\Article;

use App\Application\Exception\ArticleNotFound;
use App\Domain\Repository\ArticleRepository;

final readonly class GetArticlePage
{
    private const int SIMILAR_ARTICLE_LIMIT = 3;

    public function __construct(private ArticleRepository $articles) {}

    public function execute(int $articleId): ArticlePage
    {
        if (!$this->articles->incrementViewCount($articleId)) {
            throw new ArticleNotFound($articleId);
        }

        $article = $this->articles->findById($articleId);

        if ($article === null) {
            throw new ArticleNotFound($articleId);
        }

        return new ArticlePage(
            article: $article,
            similarArticles: $this->articles->findSimilar(
                articleId: $articleId,
                limit: self::SIMILAR_ARTICLE_LIMIT,
            ),
        );
    }
}
