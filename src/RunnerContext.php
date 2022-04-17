<?php

namespace CHEZ14\Ilgar;

use Psr\Log\LoggerInterface;

/**
 * Get current runner information. Currently limited to get logger instance for
 * logging purposes.
 */
interface RunnerContext
{
    /**
     * Gets logger instance for logging things.
     *
     * @param string|null $namespace Namespace for the logger instance.
     * @return LoggerInterface
     */
    public function getLogger(?string $namespace = null): LoggerInterface;
}
