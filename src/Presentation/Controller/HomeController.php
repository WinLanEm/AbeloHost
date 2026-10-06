<?php

declare(strict_types=1);

namespace App\Presentation\Controller;

use App\Application\Home\GetHomePage;
use App\Application\View\ViewRenderer;
use App\Presentation\Http\Request;
use App\Presentation\Http\Response;
use App\Presentation\View\BlogViewData;

final readonly class HomeController
{
    public function __construct(
        private GetHomePage $getHomePage,
        private ViewRenderer $renderer,
        private BlogViewData $viewData,
    ) {}

    public function __invoke(Request $request): Response
    {
        return Response::html($this->renderer->render(
            'home.tpl',
            $this->viewData->home($this->getHomePage->execute()),
        ));
    }
}
