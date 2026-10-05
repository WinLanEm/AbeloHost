<?php

declare(strict_types=1);

namespace App\Presentation\Http;

use InvalidArgumentException;

final readonly class Response
{
    /**
     * @param array<string, string> $headers
     */
    public function __construct(
        private string $body = '',
        private int $statusCode = 200,
        private array $headers = [],
    ) {
        if ($statusCode < 100 || $statusCode > 599) {
            throw new InvalidArgumentException('Response status code must be between 100 and 599.');
        }
    }

    public static function html(string $body, int $statusCode = 200): self
    {
        return new self(
            body: $body,
            statusCode: $statusCode,
            headers: ['Content-Type' => 'text/html; charset=UTF-8'],
        );
    }

    public static function text(string $body, int $statusCode = 200): self
    {
        return new self(
            body: $body,
            statusCode: $statusCode,
            headers: ['Content-Type' => 'text/plain; charset=UTF-8'],
        );
    }

    public function body(): string
    {
        return $this->body;
    }

    public function statusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @return array<string, string>
     */
    public function headers(): array
    {
        return $this->headers;
    }

    public function send(): void
    {
        http_response_code($this->statusCode);

        foreach ($this->headers as $name => $value) {
            header(sprintf('%s: %s', $name, $value));
        }

        echo $this->body;
    }
}
