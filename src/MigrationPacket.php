<?php
Namespace Chez14\Ilgar;

/**
 * Migration Packet base class.
 */
abstract class MigrationPacket {

    /**
     * Migration worker function.
     */
    abstract public function on_migrate();

    /**
     * Rollback function, needed as if the migration
     * failed, this will handle the problem.
     */
    abstract public function on_failed(\Exception $e);
    
    /**
     * Pre-migration event handler
     */
    public function pre_migrate() {

    }

    /**
     * Post-migration event handler
     */
    public function post_migrate() {

    }

    /**
     * Check whether this packet is aplicable.
     * 
     * @return Boolean
     */
    public function is_migratable() {
        return true;
    }
}