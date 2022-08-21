<?php

namespace CHEZ14\Ilgar\Util;

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
     * @return int
     */
    public function getLatestBatch(): int;

    /**
     * Get All Migrations that have been ran and recorded in the DB.
     *
     * @param int|null $batchNumber Number of batch we want to pick
     *
     * @return array
     */
    public function getMigrations(?int $batchNumber = null): array;

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
     * @param bool $refreshCursor Refresh the cursor mapper.
     * @return void
     */
    public function createTable(bool $refreshCursor = true): void;

    /**
     * Checks if Table has been made.
     *
     * @return bool
     */
    public function hasTable(): bool;

    /**
     * Clean migration DB.
     *
     * @return void
     */
    public function resetMigration(): void;
}
