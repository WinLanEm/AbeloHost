<?php

declare(strict_types=1);

namespace App\Presentation\Controller;

use App\Application\View\ViewRenderer;
use App\Presentation\Http\Request;
use App\Presentation\Http\Response;
use LogicException;

final readonly class ArticleController
{
    public function __construct(private ViewRenderer $renderer) {}

    /**
     * @param array<string, string> $routeParameters
     */
    public function __invoke(Request $request, array $routeParameters): Response
    {
        $articleId = $routeParameters['id'] ?? throw new LogicException('Article route requires an id.');

        return Response::html($this->renderer->render('article.tpl', [
            'pageTitle' => sprintf('Статья #%s', $articleId),
            'articleId' => $articleId,
            'articlePath' => $request->path(),
        ]));
    }
}
