<?php

declare(strict_types=1);

namespace App\Application\View;

interface ViewRenderer
{
    /**
     * @param array<string, mixed> $data
     */
    public function render(string $template, array $data = []): string;
}
