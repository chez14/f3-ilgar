<?php

    namespace Migration;

class Hardfail extends \CHEZ14\Ilgar\Migration
{
    public function onMigrate(): void
    {
        throw new \Exception('Hard fail!');
    }

    public function onFailed(\Exception $e): void
    {
    }
}
