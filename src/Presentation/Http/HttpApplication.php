<?php

declare(strict_types=1);

namespace App\Presentation\Http;

use Throwable;

final readonly class HttpApplication
{
    public function __construct(
        private Router $router,
        private ExceptionHandler $exceptionHandler,
    ) {}

    public function handle(Request $request): Response
    {
        try {
            return $this->router->dispatch($request);
        } catch (Throwable $exception) {
            return $this->exceptionHandler->handle($exception);
        }
    }
}
