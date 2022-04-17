<?php

namespace CHEZ14\Ilgar\Util;

use CHEZ14\Ilgar\Runner;
use DB\SQL\Mapper;

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
        $this->cursor = new Mapper($db, $runner->getConfig(Runner::CONFIG_TABLENAME));
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
     * @return array
     */
    public function getLatestBatch(): array
    {
        return $this->cursor->select("batch", null, [
            "group" => "batch",
            "order" => "batch desc",
            "limit" => 1
        ]);
    }

    /**
     * Get All Migrations that have been ran and recorded in the DB.
     *
     * @return array
     */
    public function getMigrations(): array
    {
        return $this->cursor->find(null, [
            "order" => "version asc"
        ]);
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
        $this->cursor->insert([
            "name" => $className,
            "version" => $version,
            "batch" => $batchNumber,
            "migrated_on" => date("Y-m-d H:i:s")
        ]);
    }

    /**
     * Get latest version that have been recorded in DB.
     *
     * @return integer
     */
    public function getLatestVersion(): int
    {
        return $this->cursor->findone("version", null, [
            "order" => "version desc",
            "limit" => 1
        ]);
    }

    /**
     * Create Migration Repo Table
     *
     * @return void
     */
    public function createTable(): void
    {
        $this->internalDB->exec(join(" ", [
            "CREATE TABLE :tablename (",
            "id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT",
            "name VARCHAR(128) NOT NULL",
            "version INTEGER NOT NULL",
            "migrated_on DATETIME NOT NULL",
            "batch INT NOT NULL",
            ")"
        ]), [
            ":tablename" => $this->runner->getConfig(Runner::CONFIG_TABLENAME)
        ]);
    }

    /**
     * Checks if Table has been made.
     *
     * @return void
     */
    public function hasTable(): bool
    {
        return !($this->internalDB->schema(
            $this->runner->getConfig(Runner::CONFIG_TABLENAME)
        ) === false);
    }
}
