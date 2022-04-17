<?php

namespace Migration;

class Softfail extends \CHEZ14\Ilgar\Migration
{
    public function run(): bool
    {
        return false;
    }
    
    public function up(): void
    {
    }

    public function down(\Exception $e): void
    {
    }
}
