<?php

declare(strict_types=1);

namespace App\Presentation\Controller;

use App\Application\Category\GetCategoryPage;
use App\Application\Exception\CategoryNotFound;
use App\Application\View\ViewRenderer;
use App\Domain\Repository\ArticleOrder;
use App\Presentation\Http\Request;
use App\Presentation\Http\Response;
use App\Presentation\View\BlogViewData;

final readonly class CategoryController
{
    public function __construct(
        private GetCategoryPage $getCategoryPage,
        private ViewRenderer $renderer,
        private BlogViewData $viewData,
    ) {}

    /**
     * @param array<string, string> $routeParameters
     */
    public function __invoke(Request $request, array $routeParameters): Response
    {
        $identifier = $routeParameters['id'] ?? '';
        $categoryId = filter_var(
            $identifier,
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 1]],
        );

        if ($categoryId === false) {
            throw new CategoryNotFound($identifier);
        }

        $order = match ($request->stringQuery('sort', ArticleOrder::PublishedAt->value)) {
            ArticleOrder::ViewCount->value => ArticleOrder::ViewCount,
            default => ArticleOrder::PublishedAt,
        };
        $categoryPage = $this->getCategoryPage->execute(
            categoryId: $categoryId,
            order: $order,
            page: $request->positiveIntegerQuery('page', 1),
        );

        return Response::html($this->renderer->render(
            'category.tpl',
            $this->viewData->category($categoryPage),
        ));
    }
}
