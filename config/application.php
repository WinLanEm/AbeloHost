<?php

declare(strict_types=1);

$environment = getenv('APP_ENV');

return [
    'debug' => $environment === 'dev',
];
