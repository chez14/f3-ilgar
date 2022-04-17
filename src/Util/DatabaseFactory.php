<?php

namespace CHEZ14\Ilgar\Util;

use CHEZ14\Ilgar\Runner;
use InvalidArgumentException;

class DatabaseFactory
{
    /**
     * Create DB Util from the DB thingy, with Runner context.
     *
     * @param mixed $db The DB
     * @param Runner $runner the Runner context
     * @return DatabaseUtilInterface
     */
    public static function createFrom(mixed $db, Runner $runner): DatabaseUtilInterface
    {
        $returnPath = null;
        if ($db instanceof \DB\SQL) {
            $returnPath = new DatabaseSQLish($db, $runner);
        } elseif ($db instanceof \DB\Mongo) {
            $returnPath = new DatabaseMongoish($db, $runner);
        } else {
            throw new InvalidArgumentException(
                sprintf("DB Class %d is not supported.", get_class($db))
            );
        }

        return $returnPath;
    }
}
