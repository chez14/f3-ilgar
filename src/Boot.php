<?php
Namespace Chez14\Ilgar;

/**
 * Ilgar starter class
 */
class Boot {
    public static function now() {
        //boot!
        $internal = \Chez14\Ilgar\Internal::instance();
        $path = \F3::instance()->get('ILGAR.path');
        \F3::instance()->route(\F3::instance()->get('ILGAR.access_path'), function($f3) use (&$internal){
            $internal->do_migrate();
        });
    }

    public static function trigger_on() {
        $internal = \Chez14\Ilgar\Internal::instance()->do_migrate();
    }
}