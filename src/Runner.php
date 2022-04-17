<?php

namespace CHEZ14\Ilgar;

use CHEZ14\Ilgar\Util\DatabaseFactory;
use InvalidArgumentException;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

class Runner extends \Prefab implements RunnerContext
{

    public const CONFIG_TABLENAME = "table";
    public const CONFIG_DB = "db";
    public const CONFIG_MIGRATIONPATH = "path";
    public const CONFIG_MIGRATIONPREFIX = "prefix";
    public const CONFIG_WEBLOG = "show_log";
    public const CONFIG_ROUTE = "access_path";
    public const CONFIG_LOGGER = "logger";
    public const CONFIG_INFO = "info";

    /**
     * Logger Internals
     *
     * @var Logger
     */
    protected $logger;

    protected $config;

    /**
     * Gets logger instance for logging things.
     *
     * @param string|null $namespace Namespace for the logger instance.
     * @return LoggerInterface
     */
    public function getLogger(?string $namespace = null): LoggerInterface
    {
        if ($namespace) {
            return $this->logger->withName($namespace);
        }
        return $this->logger;
    }

    /**
     * Run the migrations from the scanned path
     *
     * @return void
     */
    protected function runMigrations()
    {
        $db = $this->getConfig(self::CONFIG_DB);
        $dbUtil = DatabaseFactory::createFrom($db, $this);

        $log = $this->getLogger('runner');

        // Create table when we can't detect them
        if (!$dbUtil->hasTable()) {
            $dbUtil->createTable();
        }

        $migrationsDone = $dbUtil->getMigrations();
        $migrationsToCheck = $this->scanMigrationFolder();

        // Do a quick mix and match.
        $migrationTodo = $this->cleanMigration($migrationsToCheck, $migrationsDone);


        // Run actual migrations!
        $batch = $dbUtil->getLatestBatch() + 1;
        $log->info(sprintf(
            "Runner found %d migrations to run. All migration in this list will " .
                "be ran will be inserted as batch no. %d.",
            count($migrationTodo),
            $batch
        ));
        $migrationRan = 0;

        foreach ($migrationTodo as $mig) {
            $log->info(sprintf("Running %s...", $mig['name']));

            // Include and create new intance of the newly included class.
            require($mig['filename']);
            $mig = new $mig['name']($this);

            if (!($mig instanceof MigrationInterface)) {
                $log->warning(sprintf("File %s are not child of MigrationInterface. Skipping..."));
                continue;
            }

            if (!$migration->run()) {
                $log->warning(sprintf("Migration %s self-reports that it doesn't run properly.", $mig['name']));
                continue;
            }

            $dbUtil->addMigration($mig['name'], $mig['version'], $batch);
            $migrationRan++;
        }

        $log->info(sprintf("Runner has run %d migration(s) successfully.", $migrationRan));
    }

    /**
     * Clean the migration list
     *
     * @param array $migrationList Original Migration List from the folder
     * @param array $migrationDone The Migration that has been done
     * @return array the left-join of Migration List and Migration Done.
     */
    protected function cleanMigration(array $migrationList, array $migrationDone): array
    {
        $m = [];

        foreach ($migrationDone as $migration) {
            $m[$migrationDone['name']] = $migrationDone;
        }

        return array_filter($migrationList, function ($migration) use (&$m) {
            return key_exists($migration['name'], $m);
        });
    }

    /**
     * Scan the migration folder for migrations. Will return a list of filename
     * and file path with the following key format:
     *
     * - `filename`: The filename with extension
     * - `path`: The full path to the file
     * - `version`: The parsed classname from the filename
     * - `name`: The parsed classname from the filename
     *
     * @return array
     */
    protected function scanMigrationFolder(): array
    {
        $path = $this->getConfig(self::CONFIG_MIGRATIONPATH);
        $prefix = $this->getConfig(self::CONFIG_MIGRATIONPREFIX);
        $migration_packages = scandir($path);
        $points = array_splice($migration_packages, 2);
        natcasesort($points);

        $log = $this->getLogger('file-walker');

        $points = array_map(function ($file) use ($path, $prefix, &$log) {
            $fname = basename($file);
            $log->info('Proccessing ' . $file);
            if (!is_file($path . $file)) {
                $log->info('Current file was not a file.');
                return null;
            }

            $components = [];

            preg_match("/([0-9]+)\-([\w\_]+).php/i", $fname, $components);

            if (!$components || !$components[1] || !$components[2]) {
                $log->warning('The file name is in not a valid name convention. Skipping...');
                return null;
            }

            $log->info("Found " . $components[2] . " (v-" . intval($components[1]) . ")");

            return [
                "filename" => $fname,
                "path" => $path . $file,
                "version" => intval($components[1]),
                "name" => $prefix . $components[2],
            ];
        }, $points);

        return array_filter($points);
    }

    /**
     * Get configuration from Runner Contexts
     *
     * @param string $configName Configuration Key
     * @return mixed
     */
    public function getConfig(string $configName): mixed
    {
        if (key_exists($configName, $this->config)) {
            return $this->config[$configName];
        }

        throw new InvalidArgumentException("Invalid config name");
    }
}
