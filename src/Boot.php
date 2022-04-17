<?php

namespace CHEZ14\Ilgar;

/**
 * Ilgar starter class
 */
class Boot
{
    /**
     * Start the migration with default settings
     *
     * @return void
     */
    public static function now()
    {
        //boot!
        $internal = \CHEZ14\Ilgar\Runner::instance();
        $internal->setupConfig();
        \F3::instance()
            ->route(
                $internal->getConfig(Runner::CONFIG_ROUTE),
                function ($f3) use (&$internal) {
                    return $internal->runMigrations();
                }
            );
    }

    /**
     * Triggers the migration to start.
     *
     * @return array of quick stats.
     */
    public static function triggerOn()
    {
        $internal = \CHEZ14\Ilgar\Runner::instance();
        $internal->setupConfig();
        return $internal->runMigrations();
    }
}
