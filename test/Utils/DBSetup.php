<?php

namespace CHEZ14\Ilgar\Test\Utils;

use Base;
use F3;

class DBSetup
{

    public static function fixEnvironment()
    {
        $envA = getenv();
        $envB = Base::instance()->get('ENV');

        $env = array_merge($envA, $envB);
        Base::instance()->set('ENV', $env);
    }

    /**
     * Setup the database so we can just call it on the test bench.
     *
     * @return \DB\SQL|\DB\Mongo
     */
    public static function setup()
    {
        self::fixEnvironment();
        // check for DB.
        $db = null;
        if (F3::instance()->get('ENV.db') == "sqllike") {
            $db = new \DB\SQL(
                F3::instance()->get('ENV.db_dsn'),
                F3::instance()->get('ENV.db_username') ?? "",
                F3::instance()->get('ENV.db_password') ?? ""
            );
        } elseif (F3::instance()->get('ENV.db') == "mongo") {
            $db = new \DB\Mongo(
                F3::instance()->get('ENV.db_dsn'),
                F3::instance()->get('ENV.db_table')
            );
        }

        if (!$db) {
            throw new \InvalidArgumentException("Database is not set.");
        }

        F3::instance()->set('DB', $db);
        F3::instance()->set('ILGAR.db', $db);
    }
}
