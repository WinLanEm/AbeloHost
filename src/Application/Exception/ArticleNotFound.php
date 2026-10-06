<?php

declare(strict_types=1);

namespace App\Application\Exception;

use RuntimeException;

final class ArticleNotFound extends RuntimeException
{
    public function __construct(int|string $identifier)
    {
        parent::__construct(sprintf('Article "%s" was not found.', $identifier));
    }
}
