<?php

    namespace Migration;

class Softfail extends \CHEZ14\Ilgar\Migration
{
    public function onMigrate(): void
    {
        return false; // triggers soft fail
    }

    public function onFailed(\Exception $e): void
    {
    }
}
