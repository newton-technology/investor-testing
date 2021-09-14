<?php
/**
 * Created by PhpStorm.
 * User: Dmitry Loshmanov
 * Date: 26.05.2020
 * Time: 21:04
 */

namespace Common\Base\Illuminate\Database\Connectors;

use Closure;
use InvalidArgumentException;
use PDO;

use Common\Base\Illuminate\Database\PostgresConnection;

use Illuminate\Database\Connection;
use Illuminate\Database\MySqlConnection;
use Illuminate\Database\SQLiteConnection;
use Illuminate\Database\SqlServerConnection;

class ConnectionFactory extends \Illuminate\Database\Connectors\ConnectionFactory
{
    /**
     * Create a new connection instance.
     *
     * @param string $driver
     * @param PDO|Closure $connection
     * @param string $database
     * @param string $prefix
     * @param array $config
     *
     * @return Connection
     *
     * @throws InvalidArgumentException
     * @noinspection DuplicatedCode
     */
    protected function createConnection($driver, $connection, $database, $prefix = '', array $config = [])
    {
        if ($resolver = Connection::getResolver($driver)) {// phpcs:ignore
            return $resolver($connection, $database, $prefix, $config);
        }

        switch ($driver) {
            case 'mysql':
                return new MySqlConnection($connection, $database, $prefix, $config);
            case 'pgsql':
                return new PostgresConnection($connection, $database, $prefix, $config);
            case 'sqlite':
                return new SQLiteConnection($connection, $database, $prefix, $config);
            case 'sqlsrv':
                return new SqlServerConnection($connection, $database, $prefix, $config);
        }

        throw new InvalidArgumentException("Unsupported driver [{$driver}]");
    }
}
