<?php

declare(strict_types=1);

namespace App\Presentation\Http;

use App\Application\View\ViewRenderer;
use InvalidArgumentException;
use Throwable;

final readonly class ExceptionHandler
{
    /**
     * @var array<class-string<Throwable>, array{statusCode: int, publicMessage: string}>
     */
    private array $exceptionMappings;

    /**
     * @param array<string, array{statusCode: int, publicMessage: string}> $exceptionMappings
     */
    public function __construct(
        private ViewRenderer $renderer,
        private bool $debug,
        array $exceptionMappings,
    ) {
        $validatedMappings = [];

        foreach ($exceptionMappings as $exceptionClass => $mapping) {
            if (!is_a($exceptionClass, Throwable::class, true)) {
                throw new InvalidArgumentException(sprintf(
                    'HTTP exception mapping key "%s" must be a throwable class.',
                    $exceptionClass,
                ));
            }

            if ($mapping['statusCode'] < 400 || $mapping['statusCode'] > 599) {
                throw new InvalidArgumentException(sprintf(
                    'HTTP status code for "%s" must be between 400 and 599.',
                    $exceptionClass,
                ));
            }

            if (trim($mapping['publicMessage']) === '') {
                throw new InvalidArgumentException(sprintf(
                    'Public message for "%s" must not be empty.',
                    $exceptionClass,
                ));
            }

            $validatedMappings[$exceptionClass] = $mapping;
        }

        $this->exceptionMappings = $validatedMappings;
    }

    public function handle(Throwable $exception): Response
    {
        $mapping = $this->exceptionMappings[$exception::class] ?? null;
        $statusCode = $mapping['statusCode'] ?? 500;

        if ($statusCode >= 500) {
            error_log((string) $exception);
        }

        $publicMessage = $mapping['publicMessage'] ?? 'Internal Server Error';

        if (!$this->debug && $statusCode >= 500) {
            $publicMessage = 'Internal Server Error';
        }

        try {
            return Response::html(
                body: $this->renderer->render('error.tpl', [
                    'pageTitle' => sprintf('%d %s', $statusCode, $publicMessage),
                    'statusCode' => $statusCode,
                    'publicMessage' => $publicMessage,
                    'debug' => $this->debug,
                    'exception' => [
                        'class' => $exception::class,
                        'message' => $exception->getMessage(),
                        'file' => $exception->getFile(),
                        'line' => $exception->getLine(),
                        'trace' => $exception->getTraceAsString(),
                    ],
                ]),
                statusCode: $statusCode,
            );
        } catch (Throwable $renderingException) {
            error_log((string) $renderingException);

            return Response::text($publicMessage, $statusCode);
        }
    }
}
