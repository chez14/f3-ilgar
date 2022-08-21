<?php

namespace CHEZ14\Ilgar\Util;

use CHEZ14\Ilgar\Runner;
use DB\Mongo\Mapper;

class DatabaseMongoish extends \Prefab implements DatabaseUtilInterface
{
    protected $internalDB = null;

    /**
     * DB Cursor Mapper
     *
     * @var Mapper
     */
    protected $cursor = null;
    protected $runner = null;

    // phpcs:ignore
    public function __construct(\DB\Mongo $db, Runner $runner)
    {
        $this->internalDB = $db;
        $this->runner = $runner;
        $this->cursor = new Mapper($db, $runner->getConfig(Runner::CONFIG_TABLENAME));
    }

    /**
     * Get all batch that have been migrated.
     *
     * @return array
     */
    public function getBatch(): array
    {
        $batches = $this->cursor->select("batch", null, [
            'group' => ["batch"]
        ]);

        if (!$batches) {
            return [];
        }

        return array_map(function ($batch) {
            return $batch['batch'];
        }, $batches);
    }

    /**
     * Get latest batch for rerolling.
     *
     * @return int
     */
    public function getLatestBatch(): int
    {
        $batch = $this->cursor->select("batch", null, [
            'group' => ["batch"],
            "order" => ["batch" => -1],
            "limit" => 1
        ]);

        if (!$batch) {
            return -1;
        }

        return $batch[0]['batch'];
    }

    /**
     * Get All Migrations that have been ran and recorded in the DB.
     *
     * @param int|null $batchNumber Number of batch we want to pick
     *
     * @return array
     */
    public function getMigrations(?int $batchNumber = null): array
    {
        $query = [];

        if ($batchNumber !== null) {
            $query = ["batch" => $batchNumber];
        }

        $migrations = $this->cursor->find($query, [
            "order" => ["version" => -1]
        ]);

        if (!$migrations) {
            return [];
        }

        return array_map(function ($migration) {
            return $migration->cast();
        }, $migrations);
    }

    /**
     * Unregister migration from DB.
     *
     * @param string $migrationName Migration Class Name. MUST be class name.
     * @return void
     */
    public function deleteMigration(string $migrationName): void
    {
        $this->cursor->erase(["name" => $migrationName]);
    }

    /**
     * Register migration to DB
     *
     * @param string $className Migration Class Name.
     * @param int $version Migration Version Name
     * @param int $batchNumber The batch number
     * @return void
     */
    public function addMigration(string $className, int $version, int $batchNumber): void
    {
        $this->cursor->reset();
        $this->cursor->copyfrom([
            "name" => $className,
            "version" => $version,
            "batch" => $batchNumber,
            "migrated_on" => date("Y-m-d H:i:s")
        ]);
        $this->cursor->insert();
    }

    /**
     * Get latest version that have been recorded in DB.
     *
     * @return integer
     */
    public function getLatestVersion(): int
    {
        $versions = $this->cursor->select("version", null, [
            "order" => ["version" => -1],
            "limit" => 1
        ]);

        if (!$versions) {
            return -1;
        }

        return $versions[0]['version'];
    }

    /**
     * Create Migration Repo Table
     *
     * @param bool $refreshCursor Refresh the cursor mapper.
     * @return void
     */
    public function createTable(bool $refreshCursor = true): void
    {
        // Afaik we don't have to create table for this. Mogno will
        // automatically create a new one from first inserted item.
    }

    /**
     * Checks if Table has been made.
     *
     * @return void
     */
    public function hasTable(): bool
    {
        // Because table is not neccessary to be made, we're assuming that every
        // Mongo db will automatically have our table.
        return true;
    }

    /**
     * Clean migration DB.
     *
     * @return void
     */
    public function resetMigration(): void
    {
        if ($this->hasTable()) {
            $this->internalDB->drop();
        }
    }
}
