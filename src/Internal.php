<?php
Namespace Chez14\Ilgar;

/**
 * Internal API
 * Providing API for people to access and manage deployment
 * settings.
 * This class have the main Migration Lifecycle.
 */
class Internal extends \Prefab {
    protected
        $setting;

    public function __construct() {
        $this->load_setting();
    }

    /**
     * Get and set the settings option.
     */
    private function load_setting() {
        $f3 = \Base::instance();
        $setting = $f3->get('ILGAR');

        // Default setting
        $this->setting = array_merge([

            "info" => dirname(__DIR__) . "/data/migration.json",
            "path" => "migration/",
            "prefix" => "Migrate\\",
            "access_path" => "GET @ilgar: /ilgar/migrate"

        ], $setting);
    }

    /**
     * Main lifecycle starter.
     */
    protected function do_migrate() {
        $path = $this->setting['path'];
        $points = array_splice(scandir($path), 2);
        natcasesort($points);

        $prefix = $this->setting['prefix'];

        $points = array_map(function($file) use ($path, $prefix){
            $fname = basename($file);
            $components = [];
            
            preg_match("/([0-9]+)\-([\w\_]+).php/i", $fname, $components);
            return [
                "version" => intval($components[1]),
                "classname" => $prefix .$components[2],
                "path" => $path . $file
            ];
        }, $points);

        $migration_path = $this->setting['info'];
        
        $current = -1;
        if(file_exists($migration_path)) {
            $migrate = file_get_contents($migration_path);
            $migrate = json_decode($migrate, true);
            if(array_key_exists('version', $migrate)) {
                $current = $migrate['version'];
            }
        }
        //filter the version here.
        $points = array_filter($points, function($data) use($current) {
            return ($data['version'] > $current);
        });
        $cls = null;
        $counter = 0;
        $failed = false;
        try {
            array_map(function($mig_point) use (&$current, &$cls, &$counter){
                include($mig_point['path']);
                //call the class:
                $cls = new $mig_point['classname']();
                if(!$cls->migrate()) {
                    throw new \Exception("Migration failed at file " . $mig_point['classname']);
                }
                $current = $mig_point['version'];
                $counter++;
            }, $points);
        } catch (\Exception $e) {
            $cls->on_failed($e);
            $failed = $e->getMessage();
        }
        //saving migration point
        file_put_contents($migration_path, json_encode([
            "version" => $current
        ]));


        // pikirin lagi si lognya
        echo "Succesfully done $counter migration\n";
        if($failed) {
            echo "But we encountered an exception: " . $failed . "\n\n";
        }
    }

    /**
     * Get current migration version
     * 
     * @return null if migration haven't been made, int if it does.
     */
    public function get_current_version() {
        $migration_path = $this->setting['info'];
        
        $current = null;
        if(file_exists($migration_path)) {
            $migrate = file_get_contents($migration_path);
            $migrate = json_decode($migrate, true);
            if(array_key_exists('version', $migrate)) {
                $current = $migrate['version'];
            }
        }

        return $current;
    }


    /**
     * Destroy current migration info
     */
    public function reset_version() {
        $migration_path = $this->setting['info'];
        if(file_exists($migration_path)) {
            kill($migration_path);
        }
    }

}