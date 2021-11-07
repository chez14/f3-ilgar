<?php

namespace CHEZ14\Ilgar;

/**
 * Migration Packet base class.
 */
abstract class Migration implements MigrationInterface
{

    /**
     * Migration executor
     *
     * @return void
     */
    public function run(): bool
    {
        try {
            if ($this->isMigratable()) {
                $this->preMigrate();
                $this->onMigrate();
                $this->postMigrate();
            }
        } catch (\Exception $e) {
            $this->onFailed($e);
            return false;
        }

        return true;
    }

    /**
     * Pre-migration event handler
     * 
     * @return void
     */
    public function preMigrate(): void
    {
    }

    /**
     * Post-migration event handler
     * 
     * @return void
     */
    public function postMigrate(): void
    {
    }

    /**
     * Check whether this packet is aplicable.
     *
     * @return bool
     */
    public function isMigratable(): bool
    {
        return true;
    }
}
