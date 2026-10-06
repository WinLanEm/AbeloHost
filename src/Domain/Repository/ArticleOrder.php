<?php

declare(strict_types=1);

namespace App\Domain\Repository;

enum ArticleOrder: string
{
    case PublishedAt = 'date';
    case ViewCount = 'views';
}
