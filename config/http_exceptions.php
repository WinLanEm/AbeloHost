<?php

declare(strict_types=1);

use App\Application\Exception\ArticleNotFound;
use App\Application\Exception\CategoryNotFound;

return [
    ArticleNotFound::class => [
        'statusCode' => 404,
        'publicMessage' => 'Article not found',
    ],
    CategoryNotFound::class => [
        'statusCode' => 404,
        'publicMessage' => 'Category not found',
    ],
];
