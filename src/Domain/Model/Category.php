<?php

declare(strict_types=1);

namespace App\Domain\Model;

use InvalidArgumentException;

final readonly class Category
{
    public function __construct(
        private int $id,
        private string $name,
        private string $description,
    ) {
        if ($id < 1) {
            throw new InvalidArgumentException('Category id must be positive.');
        }

        if (trim($name) === '') {
            throw new InvalidArgumentException('Category name must not be empty.');
        }
    }

    public function id(): int
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function description(): string
    {
        return $this->description;
    }
}
