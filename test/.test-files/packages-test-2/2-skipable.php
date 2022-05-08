<?php

    namespace Migration;

class Skipable extends \CHEZ14\Ilgar\Migration
{
    protected $enabled = false;

    public function up(): void
    {
        $this->logger->info("Hello from Test01 Migration package");
    }

    public function down(?\Exception $e): void
    {
    }
}
