<?php

namespace CHEZ14\Ilgar;

use CHEZ14\Ilgar\Util\DatabaseFactory;
use InvalidArgumentException;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

/**
 * Runner Class is the main heart of this project. This class responsible to
 * scan, coordinate and run the migration scripts, inform the DB class to save
 * those information and provide small stats data to the callers.
 *
 * You're not supposed to directly trigger the {@see Runner::runMigration()}
 * functions directly. If you want to do so, we're reccomending you to trigger
 * run the migration via {@see Boot::triggerOn()}.
 */
class Runner extends \Prefab implements RunnerContext
{
    /**
     * Configuration table key. Default configuration will automatically fills to `migration`.
     */
    public const CONFIG_TABLENAME = "table";
    public const CONFIG_DB = "db";
    public const CONFIG_MIGRATIONPATH = "path";
    public const CONFIG_MIGRATIONPREFIX = "prefix";
    public const CONFIG_WEBLOG = "show_log";
    public const CONFIG_ROUTE = "access_path";
    public const CONFIG_LOGGER = "logger";

    /**
     * Logger Internals
     *
     * @var Logger
     */
    protected $logger;

    /**
     * Configuration for this particular class for quicker access.
     *
     * @var array
     */
    protected $config = [];

    /**
     * Our internal statistics
     *
     * @var array
     */
    protected $stats = [
        "success" => 0,
        "last_exception" => null,
        "version" => 0
    ];

    /**
     * Setup our current configuration
     *
     * @param boolean $updateToF3 Update the default config to FatFree's Registry.
     *
     * @return void
     */
    public function setupConfig($updateToF3 = true)
    {
        $f3 = \Base::instance();
        $setting = $f3->get('ILGAR');
        // This are default configurations
        $config = [
            self::CONFIG_MIGRATIONPATH => "migration/",
            self::CONFIG_MIGRATIONPREFIX => "Migration\\",
            self::CONFIG_WEBLOG => true,
            self::CONFIG_ROUTE => "GET @ilgar: /ilgar/migrate",
            self::CONFIG_DB => $f3->get('DB'),
            self::CONFIG_TABLENAME => "migrations",
        ];

        $config = array_merge($config, $setting);

        if (!$config[self::CONFIG_LOGGER]) {
            $logger = new Logger('migration');
            $config[self::CONFIG_LOGGER] = $logger;
        }

        if ($config[self::CONFIG_WEBLOG]) {
            if (!(php_sapi_name() === 'cli')) {
                header('Content-type: text/plain');
            }

            $file = "php://output";
            $logger->pushHandler(new StreamHandler($file, Logger::INFO));
        }

        // Update the config to our internals
        $this->config = $config;
        $this->logger = $this->getConfig(self::CONFIG_LOGGER);

        // Update the config to Fatfree
        if ($updateToF3) {
            $f3->set('ILGAR', $config);
        }
    }

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
     * NOT RECOMENDED FOR PUBLIC USE. PLEASE USE {@see Boot::triggerOn()}.
     *
     * @return void
     */
    public function runMigrations()
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

        // Variables for reporting
        $migrationRan = 0;
        $failed = null;
        $latestRanVersion = null;
        try {
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
                    throw new \RuntimeException(sprintf(
                        "Migration %s self-reports that it doesn't run properly.",
                        $mig['name']
                    ));
                }

                $dbUtil->addMigration($mig['name'], $mig['version'], $batch);
                $migrationRan++;
                $latestRanVersion = $mig['version'];
            }
            $log->info(sprintf("Runner has run %d migration(s) successfully.", $migrationRan));
        } catch (\Exception $e) {
            $log->critical(sprintf("Runner has encountered an error: %s.", $e->getMessage()));
            $log->debug("Here's the trace");
            $log->debug($e->getTraceAsString());
            $failed = $e;
        }

        $this->stats = [
            "success" => $migrationRan,
            "last_exception" => $failed,
            "version" => $latestRanVersion
        ];
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
     * @since 2.0.0
     * @param string $configName Configuration Key
     * @return mixed|object
     */
    public function getConfig(string $configName)
    {
        if (key_exists($configName, $this->config)) {
            return $this->config[$configName];
        }

        throw new InvalidArgumentException("Invalid config name");
    }

    /**
     * Destroy current migration info
     *
     * @return void
     */
    public function resetVersion(): void
    {
    }

    /**
     * Get current migration version
     *
     * @since 1.0.0
     * @deprecated This function will be deprecated in next major release, use
     * {@see Runner::getCurrentVersion()} instead.
     * @return integer|null
     */
    public function get_current_version(): mixed // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    {
        return $this->getCurrentVersion();
    }

    /**
     * Get current migration version
     * @since 2.0.0
     * @return integer|null
     */
    public function getCurrentVersion(): mixed
    {
        return null;
    }

    /**
     * Get last migration stats.
     *
     * This function will return an array with the following keys:
     * - success int → Number of migration ran on last run.
     * - last_exception Exception|null→ Last exception occured
     * - version int → Currently ran version.
     *
     * @since 2.0.0
     * @return array
     */
    public function getStats(): array
    {
        return $this->stat;
    }

    /**
     * Get last migration stats.
     *
     * This function will return an array with the following keys:
     * - success int → Number of migration ran on last run.
     * - last_exception Exception|null→ Last exception occured
     * - version int → Currently ran version.
     *
     * @since 1.0.0
     * @deprecated This function will be deprecated in next major release, use
     * {@see Runner::getCurrentVersion()} instead.
     * @return array
     */
    public function get_stats(): array // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    {
        return $this->getStats();
    }
}
