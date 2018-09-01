<?php
    namespace Migration;

    class Hardfail extends \Chez14\Ilgar\MigrationPacket {
        public function on_migrate(){
            throw new \Exception('Hard fail!');
        }

        public function on_failed(\Exception $e) {
            
        }
    }