<?php

declare(strict_types=1);

namespace Tests\Support;

use App\Infrastructure\Persistence\MySql\MigrationRunner;
use App\Infrastructure\Persistence\MySql\PdoConnection;
use LogicException;
use PDO;

final class TestDatabase
{
    private static ?PDO $connection = null;

    public static function boot(): void
    {
        if (self::$connection !== null) {
            return;
        }

        $applicationDatabase = self::requiredEnvironment('DB_NAME');
        $testDatabase = self::requiredEnvironment('TEST_DB_NAME');

        if (preg_match('/^[a-zA-Z0-9_]+_test$/', $testDatabase) !== 1) {
            throw new LogicException('TEST_DB_NAME must be a valid identifier ending with "_test".');
        }

        if ($testDatabase === $applicationDatabase) {
            throw new LogicException('The test and application database names must be different.');
        }

        self::setEnvironment('DB_NAME', $testDatabase);

        /** @var array{host: string, port: int, database: string, username: string, password: string} $config */
        $config = require dirname(__DIR__, 2) . '/config/database.php';

        $provider = new PdoConnection(
            host: $config['host'],
            port: $config['port'],
            database: $config['database'],
            username: $config['username'],
            password: $config['password'],
        );

        self::$connection = $provider->getConnection();

        $migrations = new MigrationRunner(
            connection: self::$connection,
            migrationDirectory: dirname(__DIR__, 2) . '/database/migrations',
        );
        $migrations->migrate();
    }

    public static function connection(): PDO
    {
        return self::$connection
            ?? throw new LogicException('The test database has not been booted.');
    }

    private static function requiredEnvironment(string $name): string
    {
        $value = getenv($name);

        if ($value === false || $value === '') {
            throw new LogicException(sprintf('%s environment variable is required.', $name));
        }

        return $value;
    }

    private static function setEnvironment(string $name, string $value): void
    {
        putenv(sprintf('%s=%s', $name, $value));
        $_ENV[$name] = $value;
        $_SERVER[$name] = $value;
    }
}
