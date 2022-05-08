<?php

namespace CHEZ14\Ilgar\Util;

use CHEZ14\Ilgar\Runner;
use DB\SQL\Mapper;
use PDOException;

class DatabaseSQLish extends \Prefab implements DatabaseUtilInterface
{
    protected $internalDB = null;
    protected $cursor = null;
    protected $runner = null;

    // phpcs:ignore
    public function __construct(\DB\SQL $db, Runner $runner)
    {
        $this->internalDB = $db;
        $this->runner = $runner;

        if (!$this->hasTable()) {
            $this->createTable(false);
        }

        $this->recreateCursor();
    }

    /**
     * Recreate cursor wuth
     *
     * @return void
     */
    protected function recreateCursor()
    {
        // This will recreate the mapper object to refresh the fields internal.
        // Should be triggered whenever the DB is reset.
        $this->cursor = new Mapper($this->internalDB, $this->runner->getConfig(Runner::CONFIG_TABLENAME));
    }

    /**
     * Get all batch that have been migrated.
     *
     * @return array
     */
    public function getBatch(): array
    {
        return $this->cursor->select("batch", null, [
            "group" => "batch"
        ]);
    }

    /**
     * Get latest batch for rerolling.
     *
     * @return int
     */
    public function getLatestBatch(): int
    {
        $batch = $this->cursor->select("batch", null, [
            "group" => "batch",
            "order" => "batch desc",
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
     * @return array
     */
    public function getMigrations(): array
    {
        $migrations = $this->cursor->select('name, version, batch, migrated_on', null, [
            "order" => "version asc"
        ]);
        return $migrations;
    }

    /**
     * Unregister migration from DB.
     *
     * @param string $className Migration Class Name. MUST be class name.
     * @return void
     */
    public function deleteMigration(string $className): void
    {
        $this->cursor->erase(["name" => $className]);
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
            "order" => "version desc",
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
        $autoincrement = "INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT";
        if (preg_match('/sqlite/i', $this->internalDB->driver())) {
            $autoincrement = "INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT";
        } elseif (preg_match('/pgsql/i', $this->internalDB->driver())) {
            $autoincrement = "SERIAL PRIMARY KEY";
        }

        $datetime = "DATETIME";
        if (preg_match('/pgsql/i', $this->internalDB->driver())) {
            $datetime = "TIME";
        }

        $this->internalDB->exec(
            sprintf(
                join(" ", [
                    "CREATE TABLE %s (",
                    "id %s,",
                    "name TEXT NOT NULL,",
                    "version INTEGER NOT NULL,",
                    "migrated_on %s NOT NULL,",
                    "batch INT NOT NULL",
                    ")"
                ]),
                $this->runner->getConfig(Runner::CONFIG_TABLENAME),
                $autoincrement,
                $datetime
            )
        );
        if ($refreshCursor) {
            $this->recreateCursor();
        }
    }

    /**
     * Checks if Table has been made.
     *
     * @return bool
     */
    public function hasTable(): bool
    {
        try {
            $tableSchemas = ($this->internalDB->schema(
                $this->runner->getConfig(Runner::CONFIG_TABLENAME)
            ));
            return !empty($tableSchemas);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Clean migration DB.
     *
     * @return void
     */
    public function resetMigration(): void
    {
        if ($this->hasTable()) {
            $this->internalDB->exec(sprintf("drop table %s", $this->runner->getConfig(Runner::CONFIG_TABLENAME)));
        }
    }
}
