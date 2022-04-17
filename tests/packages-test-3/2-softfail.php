<?php

    namespace Migration;

class Softfail extends \CHEZ14\Ilgar\Migration
{
    public function up(): void
    {
        return false; // triggers soft fail
    }

    public function down(\Exception $e): void
    {
    }
}
