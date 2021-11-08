<?php

    namespace Migration;

class Test03 extends \CHEZ14\Ilgar\Migration
{
    public function onMigrate(): void
    {
        echo "Hello from Test02 Migration package";
    }

    public function onFailed(\Exception $e): void
    {
    }
}
