<?php

namespace CHEZ14\Ilgar;

/**
 * Migration Packet base class.
 *
 * @deprecated This class will be removed in future major release
 */
abstract class MigrationPacket extends Migration
{

    /**
     * Migration worker function.
     *
     * @return void
     */
    public function up(): void
    {
        $this->logger->warn(
            "This style of migration will be deprecated in future " .
                "major release, please consult to documentation for more information."
        );
        // this function will supports older version of ilgar.
        $this->logger->info("Running Pre-migration event...");
        $this->pre_migrate();
        $this->logger->notice("Pre-migration ran.");
        $this->logger->info("Running actual migration...");
        $this->on_migrate();
        $this->logger->notice("Actual migration ran.");
        $this->logger->info("Running Post-migration event....");
        $this->post_migrate();
        $this->logger->notice("Post-migration ran.");
        return;
    }

    /**
     * Rollback function, needed as if the migration failed, this will handle
     * the problem.
     *
     * @param \Exception $e Error context when error has
     * @return void
     */
    public function down(?\Exception $e): void
    {
        // this function will supports older version of ilgar.
        $this->on_failed($e);
        return;
    }

    // phpcs:disable
    /**
     * Migration worker function.
     *
     * @deprecated This method will be deprecated in next major release, please
     * use `migrate` instead,
     * @see Migration::up()
     * @return void
     */
    public function on_migrate()
    {
    }

    /**
     * Pre-migration event handler
     * 
     * @deprecated This method will be deprecated in next major release
     * @return void
     * phpcs-ignore-line
     */
    public function pre_migrate(): void
    {
    }

    /**
     * Post-migration event handler
     * 
     * @deprecated This method will be deprecated in next major release
     * @return void
     */
    public function post_migrate(): void
    {
    }


    /**
     * Check whether this packet is aplicable.
     *
     * @deprecated This method will be deprecated in next major release
     * @return bool
     */
    public function is_migratable(): bool
    {
        return true;
    }

    /**
     * Rollback function, needed as if the migration failed, this will handle
     * the problem.
     *
     * @deprecated This method will be deprecated in next major release
     * @param \Exception $e Error contexts
     * @return mixed
     */
    public function on_failed(\Exception $e)
    {
    }
    // phpcs:enable
}
