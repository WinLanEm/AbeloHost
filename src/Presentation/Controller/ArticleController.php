<?php

declare(strict_types=1);

namespace App\Presentation\Controller;

use App\Application\Article\GetArticlePage;
use App\Application\Exception\ArticleNotFound;
use App\Application\View\ViewRenderer;
use App\Presentation\Http\Request;
use App\Presentation\Http\Response;
use App\Presentation\View\BlogViewData;

final readonly class ArticleController
{
    public function __construct(
        private GetArticlePage $getArticlePage,
        private ViewRenderer $renderer,
        private BlogViewData $viewData,
    ) {}

    /**
     * @param array<string, string> $routeParameters
     */
    public function __invoke(Request $request, array $routeParameters): Response
    {
        $identifier = $routeParameters['id'] ?? '';
        $articleId = filter_var(
            $identifier,
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 1]],
        );

        if ($articleId === false) {
            throw new ArticleNotFound($identifier);
        }

        $articlePage = $this->getArticlePage->execute($articleId);

        return Response::html($this->renderer->render(
            'article.tpl',
            $this->viewData->article($articlePage),
        ));
    }
}
