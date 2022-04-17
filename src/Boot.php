<?php

namespace CHEZ14\Ilgar;

/**
 * F3-Ilgar Bootloaders and macros.
 *
 * Responsible to initiate and register Ilgar to F3-System in the right time.
 * Additionally this class gives a little bit macro script for public to
 * self-start the migration script run sequence programatically.
 *
 * @since 1.0.0
 */
class Boot
{
    /**
     * Start the migration with default settings
     *
     * @since 1.0.0
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
     * @since 2.0.0
     * @return void
     */
    public static function triggerOn(): void
    {
        $internal = \CHEZ14\Ilgar\Runner::instance();
        $internal->setupConfig();
        $internal->runMigrations();
    }

    /**
     * Triggers the migration to start.
     * @deprecated This function will be deprecated in next major relase, please
     * use {@see Boot::triggerOn()} instead.
     *
     * @since 1.0.0
     *
     * @return array For quicker stats
     */
    public static function trigger_on() // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    {
        self::triggerOn();
        return \CHEZ14\Ilgar\Runner::instance()->getStats();
    }
}
