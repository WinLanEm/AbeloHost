<?php

declare(strict_types=1);

namespace App\Domain\Model;

use DateTimeImmutable;
use InvalidArgumentException;

final readonly class ArticleSummary
{
    public function __construct(
        private int $id,
        private string $imagePath,
        private string $title,
        private string $description,
        private DateTimeImmutable $publishedAt,
        private int $viewCount,
    ) {
        if ($id < 1) {
            throw new InvalidArgumentException('Article id must be positive.');
        }

        if (trim($imagePath) === '') {
            throw new InvalidArgumentException('Article image path must not be empty.');
        }

        if (trim($title) === '') {
            throw new InvalidArgumentException('Article title must not be empty.');
        }

        if ($viewCount < 0) {
            throw new InvalidArgumentException('Article view count must not be negative.');
        }
    }

    public function id(): int
    {
        return $this->id;
    }

    public function imagePath(): string
    {
        return $this->imagePath;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function publishedAt(): DateTimeImmutable
    {
        return $this->publishedAt;
    }

    public function viewCount(): int
    {
        return $this->viewCount;
    }
}
