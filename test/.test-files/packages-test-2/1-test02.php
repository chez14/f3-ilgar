<?php

namespace Migration;

class Test02 extends \CHEZ14\Ilgar\Migration
{
    public function up(): void
    {
        $this->logger->info("Hello from Test01 Migration package");
    }

    public function down(?\Exception $e): void
    {
    }
}
