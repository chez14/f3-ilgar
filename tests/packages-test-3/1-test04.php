<?php

    namespace Migration;

class Test04 extends \CHEZ14\Ilgar\Migration
{
    public function onMigrate(): void
    {
        echo "Hello from Test01 Migration package";
    }

    public function onFailed(\Exception $e): void
    {
    }
}
