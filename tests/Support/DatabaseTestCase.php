<?php

declare(strict_types=1);

namespace Tests\Support;

use PDO;
use PHPUnit\Framework\TestCase;

abstract class DatabaseTestCase extends TestCase
{
    protected PDO $connection;

    protected DatabaseFixtures $fixtures;

    protected function setUp(): void
    {
        parent::setUp();

        $this->connection = TestDatabase::connection();
        $this->connection->beginTransaction();
        $this->fixtures = new DatabaseFixtures($this->connection);
    }

    protected function tearDown(): void
    {
        if ($this->connection->inTransaction()) {
            $this->connection->rollBack();
        }

        parent::tearDown();
    }
}
