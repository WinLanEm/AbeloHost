<?php

declare(strict_types=1);

namespace App\Infrastructure\View;

use App\Application\View\ViewRenderer;
use RuntimeException;
use Smarty\Smarty;

final class SmartyRenderer implements ViewRenderer
{
    private readonly Smarty $smarty;

    public function __construct(
        string $templateDirectory,
        string $compileDirectory,
    ) {
        $this->ensureDirectoryExists($compileDirectory);

        $this->smarty = new Smarty();
        $this->smarty->setTemplateDir($templateDirectory);
        $this->smarty->setCompileDir($compileDirectory);
    }

    public function render(string $template, array $data = []): string
    {
        $this->smarty->assign($data);

        return $this->smarty->fetch($template);
    }

    private function ensureDirectoryExists(string $directory): void
    {
        if (is_dir($directory)) {
            return;
        }

        if (!mkdir($directory, 0775, true) && !is_dir($directory)) {
            throw new RuntimeException(sprintf('Could not create runtime directory "%s".', $directory));
        }
    }
}
