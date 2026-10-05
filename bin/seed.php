<?php

declare(strict_types=1);

use App\Infrastructure\Persistence\MySql\PdoConnection;

require dirname(__DIR__) . '/vendor/autoload.php';

/** @var array{host: string, port: int, database: string, username: string, password: string} $databaseConfig */
$databaseConfig = require dirname(__DIR__) . '/config/database.php';
$seedFile = dirname(__DIR__) . '/database/seeds/blog.sql';
$sql = file_get_contents($seedFile);

if ($sql === false || trim($sql) === '') {
    fwrite(STDERR, sprintf("Seed file \"%s\" is empty or unreadable.\n", $seedFile));
    exit(1);
}

try {
    $connection = new PdoConnection(
        host: $databaseConfig['host'],
        port: $databaseConfig['port'],
        database: $databaseConfig['database'],
        username: $databaseConfig['username'],
        password: $databaseConfig['password'],
    );
    $pdo = $connection->getConnection();

    $pdo->beginTransaction();

    try {
        $pdo->exec($sql);
        $pdo->commit();
    } catch (Throwable $exception) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        throw $exception;
    }

    fwrite(STDOUT, "Seeded 4 categories and 9 articles.\n");
} catch (Throwable $exception) {
    fwrite(STDERR, sprintf("Seeding failed: %s\n", $exception->getMessage()));
    exit(1);
}
