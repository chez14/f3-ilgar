<?php

    namespace Migration;

class Test03 extends \CHEZ14\Ilgar\Migration
{
    public function up(): void
    {
        echo "Hello from Test02 Migration package";
    }

    public function down(\Exception $e): void
    {
    }
}
