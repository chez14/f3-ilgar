<?php

    namespace Migration;

class Skipable extends \CHEZ14\Ilgar\Migration
{
    public function onMigrate() : void
    {
        echo "Hello from Test01 Migration package";
    }

    public function onFailed(\Exception $e) : void
    {
    }

    public function is_migratable()
    {
        return false;
    }
}
