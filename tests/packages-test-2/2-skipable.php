<?php
    namespace Migration;

    class Skipable extends \Chez14\Ilgar\MigrationPacket {
        public function on_migrate(){
            echo "Hello from Test01 Migration package";
        }

        public function on_failed(\Exception $e) {
            
        }

        public function is_migratable() {
            return false;
        }
    }