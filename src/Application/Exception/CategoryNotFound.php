<?php

declare(strict_types=1);

namespace App\Application\Exception;

use RuntimeException;

final class CategoryNotFound extends RuntimeException
{
    public function __construct(int|string $identifier)
    {
        parent::__construct(sprintf('Category "%s" was not found.', $identifier));
    }
}
