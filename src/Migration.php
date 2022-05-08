<?php

namespace CHEZ14\Ilgar;

use Monolog\Logger;
use RuntimeException;

/**
 * Migration Packet base class.
 *
 * Extend this class and put it in your migration folder.
 *
 * @since 2.0.0
 */
abstract class Migration implements MigrationInterface
{
    /**
     * @var RunnerContext
     */
    protected $runnerContext = null;

    /**
     * For logging
     *
     * @var Logger
     */
    protected $logger = null;

    /**
     * Sets the migration disabled.
     *
     * @var boolean
     */
    protected $enabled = true;

    /**
     * Inititates migration before actually running it.
     *
     * @param RunnerContext $runnerContext Runner context for this Migration
     */
    public function __construct(RunnerContext $runnerContext)
    {
        $this->runnerContext = $runnerContext;
        $this->logger = $runnerContext->getLogger(static::class);
    }

    /**
     * Migration executor
     *
     * @return void
     */
    public function run(): bool
    {
        $logger = $this->logger;

        if (!$this->enabled) {
            $logger->notice("This migration is set as disabled. Skipping...");
            return false;
        }

        try {
            $logger->info("Running migration...");
            $this->up();
            $logger->notice("Migration ran");
        } catch (\Throwable $e) {
            $logger->error("Runner catched an Exception");
            $logger->error(sprintf("%d: %s", $e->getCode(), $e->getMessage()));
            $logger->debug(sprintf("From %s#%d", $e->getFile(), $e->getLine()));
            $logger->debug(sprintf("Stacktrace: %s", $e->getTraceAsString()));

            throw new RuntimeException("Migration has encountered an exception", 12, $e);
        }
        return true;
    }
}
