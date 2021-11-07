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
        $internal = \Chez14\Ilgar\Internal::instance();
        $path = \F3::instance()->get('ILGAR.path');
        \F3::instance()
            ->route(
                \F3::instance()->get('ILGAR.access_path'),
                function ($f3) use (&$internal) {
                    $internal->doMigrate();
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
        return \Chez14\Ilgar\Internal::instance()->doMigrate();
    }
}
