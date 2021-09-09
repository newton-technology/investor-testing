<?php

namespace Common\Base\Illuminate\Database;

use Closure;
use Throwable;

use Illuminate\Support\Facades\DB;

/**
 * Class Transaction
 * @package App\Utils\Database
 */
class Transaction
{
    /**
     * @param string $connection
     * @param Closure $func
     * @throws Throwable
     */
    public function execute(string $connection, Closure $func)
    {
        $connection = DB::connection($connection);

        try {
            $connection->beginTransaction();
            $func();
            $connection->commit();
        } catch (Throwable $throwable) {
            $connection->rollBack();
            throw $throwable;
        }
    }
}
