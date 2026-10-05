<?php

declare(strict_types=1);

use App\Infrastructure\Persistence\MySql\MigrationRunner;
use App\Infrastructure\Persistence\MySql\PdoConnection;

require dirname(__DIR__) . '/vendor/autoload.php';

/** @var array{host: string, port: int, database: string, username: string, password: string} $databaseConfig */
$databaseConfig = require dirname(__DIR__) . '/config/database.php';

try {
    $connection = new PdoConnection(
        host: $databaseConfig['host'],
        port: $databaseConfig['port'],
        database: $databaseConfig['database'],
        username: $databaseConfig['username'],
        password: $databaseConfig['password'],
    );

    $runner = new MigrationRunner(
        connection: $connection->getConnection(),
        migrationDirectory: dirname(__DIR__) . '/database/migrations',
    );

    $appliedMigrations = $runner->migrate();

    if ($appliedMigrations === []) {
        fwrite(STDOUT, "No migrations to apply.\n");
        exit(0);
    }

    foreach ($appliedMigrations as $migration) {
        fwrite(STDOUT, sprintf("Applied %s\n", $migration));
    }
} catch (Throwable $exception) {
    fwrite(STDERR, sprintf("Migration failed: %s\n", $exception->getMessage()));
    exit(1);
}
