<?php

    namespace Migration;

class Hardfail extends \CHEZ14\Ilgar\Migration
{
    public function up(): void
    {
        throw new \Exception('Hard fail!');
    }

    public function down(?\Exception $e): void
    {
    }
}
