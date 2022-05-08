<?php

namespace CHEZ14\Ilgar\Test\Utils;

use F3;

class DBSetup
{
    /**
     * Setup the database so we can just call it on the test bench.
     *
     * @return \DB\SQL|\DB\Mongo
     */
    public static function setup()
    {
        // check for DB.
        $db = null;
        if ($_ENV['db'] == "sqllike") {
            $db = new \DB\SQL($_ENV['db_dsn'], $_ENV['db_username'] ?? "", $_ENV['db_password'] ?? "");
        } elseif ($_ENV['db'] == "mongo") {
            $db = new \DB\Mongo($_ENV['db_dsn'], $_ENV['db_table']);
        }

        if (!$db) {
            throw new \InvalidArgumentException("Database is not set.");
        }

        F3::instance()->set('DB', $db);
        F3::instance()->set('ILGAR.db', $db);
    }
}
