<?php
/**
 * Created by PhpStorm.
 * User: Dmitry Loshmanov
 * Date: 17.03.2021
 * Time: 16:39
 */

namespace Common\Base\Exception;

class PDOException extends \PDOException
{
    protected string $connection;
    protected array $bindings;
    protected string $query;

    /**
     * @return string
     */
    public function getConnection(): string
    {
        return $this->connection;
    }

    /**
     * @param string $connection
     * @return PDOException
     */
    public function setConnection(string $connection): PDOException
    {
        $this->connection = $connection;
        return $this;
    }

    /**
     * @return array
     */
    public function getBindings(): array
    {
        return $this->bindings;
    }

    /**
     * @param array $bindings
     * @return PDOException
     */
    public function setBindings(array $bindings): PDOException
    {
        $this->bindings = $bindings;
        return $this;
    }

    /**
     * @return string
     */
    public function getQuery(): string
    {
        return $this->query;
    }

    /**
     * @param string $query
     * @return PDOException
     */
    public function setQuery(string $query): PDOException
    {
        $this->query = $query;
        return $this;
    }

    public function getSQL()
    {
        //
    }
}
