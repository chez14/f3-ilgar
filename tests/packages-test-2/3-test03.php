<?php
    namespace Migration;

    class Test03 extends \Chez14\Ilgar\MigrationPacket {
        public function on_migrate(){
            echo "Hello from Test02 Migration package";
        }

        public function on_failed(\Exception $e) {
            
        }
    }