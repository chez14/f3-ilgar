<?php

namespace Tests\Integration;

use Base;
use CHEZ14\Ilgar\Runner;
use CHEZ14\Ilgar\Util\DatabaseFactory;
use CHEZ14\Ilgar\Util\DatabaseUtilInterface;

/**
 * DBMigrationBatchFetcherTest
 * @group Database
 */
class DBMigrationBatchFetcherTest extends BaseTest
{
    /**
     * DB Typings
     *
     * @var DatabaseUtilInterface
     */
    protected $db = null;
    protected function setUp(): void
    {
        parent::setUp();
        \CHEZ14\Ilgar\Boot::now();

        $db = DatabaseFactory::createFrom(Base::instance()->get('DB'), Runner::instance());

        // make sure migration is currently empty.
        $db->resetMigration();

        if (!$db->hasTable()) {
            $db->createTable();
        }
        $this->db = $db;
    }

    protected function tearDown(): void
    {
        $this->db->resetMigration();
    }


    /**
     * @test
     * @testdox  Able to handle query with balnk table
     * @covers \CHEZ14\Ilgar\Util\DatabaseUtilInterface->getBatch(),
     * \CHEZ14\Ilgar\Util\DatabaseUtilInterface->getMigrations(),
     * \CHEZ14\Ilgar\Util\DatabaseSQLish->getBatch(),
     * \CHEZ14\Ilgar\Util\DatabaseSQLish->getMigrations(),
     * \CHEZ14\Ilgar\Util\DatabaseMongoish->getBatch(),
     * \CHEZ14\Ilgar\Util\DatabaseMongoish->getMigrations(),
     */
    public function fetchMigrationWithEmptyTable()
    {
        $db = $this->db;
        $this->assertCount(0, $db->getBatch());
        $this->assertCount(0, $db->getMigrations());
        $this->assertCount(0, $db->getMigrations(0));
        $this->assertCount(0, $db->getMigrations(1));
        $this->assertCount(0, $db->getMigrations(2));
        $this->assertCount(0, $db->getMigrations(3));
        $this->assertCount(0, $db->getMigrations(10));
        $this->assertCount(0, $db->getMigrations(100));
    }

    /**
     * @test
     * @testdox  Able to fetch migrations within the batch
     *
     * @covers \CHEZ14\Ilgar\Util\DatabaseUtilInterface->getBatch(),
     * \CHEZ14\Ilgar\Util\DatabaseUtilInterface->getMigrations(),
     * \CHEZ14\Ilgar\Util\DatabaseSQLish->getBatch(),
     * \CHEZ14\Ilgar\Util\DatabaseSQLish->getMigrations(),
     * \CHEZ14\Ilgar\Util\DatabaseMongoish->getBatch(),
     * \CHEZ14\Ilgar\Util\DatabaseMongoish->getMigrations(),
     */
    public function fetchMigrationWithinBatch()
    {
        $db = $this->db;

        // adding several mgirations
        $migrations = [
            ["name" => "MigrationA1", "ver" => 1, "batch" => 0],
            ["name" => "MigrationA2", "ver" => 2, "batch" => 0],
            ["name" => "MigrationA3", "ver" => 3, "batch" => 0],
            ["name" => "MigrationA4", "ver" => 4, "batch" => 0],
            ["name" => "MigrationA5", "ver" => 5, "batch" => 0],
            ["name" => "MigrationB1", "ver" => 6, "batch" => 1],
            ["name" => "MigrationB2", "ver" => 7, "batch" => 1],
            ["name" => "MigrationC1", "ver" => 8, "batch" => 2],
            ["name" => "MigrationC2", "ver" => 9, "batch" => 2],
            ["name" => "MigrationD1", "ver" => 10, "batch" => 3],
            ["name" => "MigrationE1", "ver" => 11, "batch" => 4],
            ["name" => "MigrationF1", "ver" => 12, "batch" => 5],
            ["name" => "MigrationG1", "ver" => 13, "batch" => 6],
        ];

        foreach ($migrations as $mig) {
            $db->addMigration($mig['name'], $mig['ver'], $mig['batch']);
        }

        // Here's the real test:
        // Batch list:
        // 0 → 5
        // 1 → 2
        // 2 → 2
        // 3 → 1
        // 4 → 1
        // 5 → 1
        // 6 → 1
        $batches = $db->getBatch();
        $this->assertIsArray($batches);
        $this->assertCount(7, $batches);
        $this->assertEqualsCanonicalizing([0, 1, 2, 3, 4, 5, 6], $batches);
        $this->assertCount(count($migrations), $db->getMigrations());
        $this->assertCount(5, $db->getMigrations(0));
        $this->assertCount(2, $db->getMigrations(1));
        $this->assertCount(2, $db->getMigrations(2));
        $this->assertCount(1, $db->getMigrations(3));
        $this->assertCount(0, $db->getMigrations(10));
        $this->assertCount(0, $db->getMigrations(100));
    }
}
