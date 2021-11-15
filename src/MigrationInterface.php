<?php

namespace CHEZ14\Ilgar;

interface MigrationInterface
{
    /**
     * Migration worker function.
     *
     * @return void No return expected.
     */
    public function up(): void;

    /**
     * Rollback function, needed as if the migration failed, this will handle
     * the problem.
     *
     * @param \Exception $e Error contexts of current migration routine
     * @return void No return expected.
     */
    public function down(?\Exception $e): void;

    /**
     * Runs the migration executor
     *
     * returns true when the migration is successfully run. Returns false if
     * there's exception.
     *
     * @return boolean
     */
    public function run(): bool;
}
