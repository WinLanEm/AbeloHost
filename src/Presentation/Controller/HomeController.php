<?php

declare(strict_types=1);

namespace App\Presentation\Controller;

use App\Application\View\ViewRenderer;

final readonly class HomeController
{
    public function __construct(private ViewRenderer $renderer) {}

    public function __invoke(): string
    {
        return $this->renderer->render('home.tpl', [
            'pageTitle' => 'PHP Blog',
        ]);
    }
}
