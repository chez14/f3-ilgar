<?php

namespace Migration;

class Test01 extends \CHEZ14\Ilgar\Migration
{
    public function up(): void
    {
        echo "Hello from Test01 Migration package";
    }

    public function down(\Exception $e): void
    {
    }
}
