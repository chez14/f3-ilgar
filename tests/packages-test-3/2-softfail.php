<?php
    namespace Migration;

    class Softfail extends \Chez14\Ilgar\MigrationPacket {
        public function on_migrate(){
            return false; // triggers soft fail
        }

        public function on_failed(\Exception $e) {
            
        }
    }