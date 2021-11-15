<?php

namespace CHEZ14\Ilgar\Util;

use DateTime;

interface DatabaseUtilInterface
{
    /**
     * Get all batch that have been migrated.
     *
     * @return array
     */
    public function getBatch(): array;

    /**
     * Get latest batch for rerolling.
     *
     * @return array
     */
    public function getLatestBatch(): array;

    /**
     * Get All Migrations that have been ran and recorded in the DB.
     *
     * @return array
     */
    public function getMigrations(): array;

    /**
     * Unregister migration from DB.
     *
     * @param string $className Migration Class Name. MUST be class name.
     * @return void
     */
    public function deleteMigration(string $className): void;

    /**
     * Register migration to DB
     *
     * @param string $className Migration Class Name.
     * @param int $version Migration Version Name
     * @param int $batchNumber the batch number
     * @return void
     */
    public function addMigration(string $className, int $version, int $batchNumber): void;

    /**
     * Get latest version that have been recorded in DB.
     *
     * @return integer
     */
    public function getLatestVersion(): int;

    /**
     * Create Migration Repo Table
     *
     * @return void
     */
    public function createTable(): void;

    /**
     * Checks if Table has been made.
     *
     * @return bool
     */
    public function hasTable(): bool;
}
