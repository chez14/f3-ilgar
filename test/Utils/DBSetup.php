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
        if (F3::instance()->get('ENV.db', getenv('db')) == "sqllike") {
            $db = new \DB\SQL(
                F3::instance()->get('ENV.db_dsn', getenv('db_dsn')),
                F3::instance()->get('ENV.db_username', getenv('db_username')) ?? "",
                F3::instance()->get('ENV.db_password', getenv('db_password')) ?? ""
            );
        } elseif (F3::instance()->get('ENV.db', getenv('db')) == "mongo") {
            $db = new \DB\Mongo(
                F3::instance()->get('ENV.db_dsn', getenv('db_dsn')),
                F3::instance()->get('ENV.db_table', getenv('db_table'))
            );
        }

        if (!$db) {
            throw new \InvalidArgumentException("Database is not set.");
        }

        F3::instance()->set('DB', $db);
        F3::instance()->set('ILGAR.db', $db);
    }
}
