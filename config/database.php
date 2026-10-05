<?php

declare(strict_types=1);

$host = getenv('DB_HOST');
$portValue = getenv('DB_PORT');
$database = getenv('DB_NAME');
$username = getenv('DB_USER');
$password = getenv('DB_PASSWORD');

$port = filter_var(
    $portValue === false ? 3306 : $portValue,
    FILTER_VALIDATE_INT,
    ['options' => ['min_range' => 1, 'max_range' => 65535]],
);

if ($port === false) {
    throw new InvalidArgumentException('DB_PORT must be a valid TCP port.');
}

return [
    'host' => $host === false || $host === '' ? 'mysql' : $host,
    'port' => $port,
    'database' => $database === false || $database === '' ? 'blog' : $database,
    'username' => $username === false || $username === '' ? 'blog' : $username,
    'password' => $password === false ? '' : $password,
];
