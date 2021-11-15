<?php

namespace CHEZ14\Ilgar\Util;

use CHEZ14\Ilgar\RunnerInterface;
use InvalidArgumentException;

class Database
{
    /**
     * Create DB Util from the DB thingy, with Runner context.
     *
     * @param mixed $db The DB
     * @param RunnerInterface $runner the Runner context
     * @return DatabaseUtilInterface
     */
    public static function createFrom(mixed $db, RunnerInterface $runner): DatabaseUtilInterface
    {
        $returnPath = null;
        if ($db instanceof \DB\SQL) {
            $returnPath = new DatabaseSQLLike($db, $runner);
        } elseif ($db instanceof \DB\Mongo) {
            $returnPath = new DatabaseMongoLike($db, $runner);
        } else {
            throw new InvalidArgumentException(
                sprintf("DB Class %d is not supported.", get_class($db))
            );
        }

        return $returnPath;
    }
}
