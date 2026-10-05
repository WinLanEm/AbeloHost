<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\MySql;

use PDO;
use RuntimeException;

final readonly class MigrationRunner
{
    public function __construct(
        private PDO $connection,
        private string $migrationDirectory,
    ) {}

    /**
     * @return list<string> Applied migration names.
     */
    public function migrate(): array
    {
        $this->createMigrationTable();

        $appliedMigrations = $this->appliedMigrations();
        $migrationFiles = glob($this->migrationDirectory . '/*.sql');

        if ($migrationFiles === false) {
            throw new RuntimeException('Could not read the migration directory.');
        }

        sort($migrationFiles, SORT_STRING);

        $appliedNow = [];

        foreach ($migrationFiles as $migrationFile) {
            $migrationName = basename($migrationFile);

            if (isset($appliedMigrations[$migrationName])) {
                continue;
            }

            $sql = file_get_contents($migrationFile);

            if ($sql === false || trim($sql) === '') {
                throw new RuntimeException(sprintf('Migration "%s" is empty or unreadable.', $migrationName));
            }

            $this->connection->exec($sql);

            $statement = $this->connection->prepare(
                'INSERT INTO schema_migrations (version) VALUES (:version)',
            );
            $statement->execute(['version' => $migrationName]);

            $appliedNow[] = $migrationName;
        }

        return $appliedNow;
    }

    private function createMigrationTable(): void
    {
        $this->connection->exec(<<<'SQL'
            CREATE TABLE IF NOT EXISTS schema_migrations (
                version VARCHAR(255) NOT NULL PRIMARY KEY,
                applied_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            SQL);
    }

    /**
     * @return array<string, true>
     */
    private function appliedMigrations(): array
    {
        $statement = $this->connection->query('SELECT version FROM schema_migrations');

        if ($statement === false) {
            throw new RuntimeException('Could not read applied migrations.');
        }

        $appliedMigrations = [];

        while (($version = $statement->fetchColumn()) !== false) {
            if (is_string($version)) {
                $appliedMigrations[$version] = true;
            }
        }

        return $appliedMigrations;
    }
}
