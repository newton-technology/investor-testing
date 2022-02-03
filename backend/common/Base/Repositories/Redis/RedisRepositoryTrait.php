<?php

namespace Common\Base\Repositories\Redis;

use Exception;
use LogicException;
use Redis;
use Generator;
use RedisSentinel;
use RuntimeException;

use Common\Base\Utils\BatchUtils;

/**
 * Trait RedisRepositoryTrait
 *
 * @property string $connection Имя соединения с Redis (смотреть в config/database.php в конкретном сервисе)
 *
 * @package Common\Repositories\Traits
 */
trait RedisRepositoryTrait
{
    protected Redis $connectionInstance;

    public function getConnectionName(): string
    {
        return $this->connection;
    }

    /**
     * @throws Exception
     */
    public function __construct()
    {
        if (empty($this->connection)) {
            throw new LogicException('missing connection for ' . self::class);
        }

        if (config('redis.cluster.enabled')) {
            $sentinel = new RedisSentinel(
                config('redis.cluster.sentinel.host'),
                config('redis.cluster.sentinel.port'),
                config('redis.cluster.sentinel.timeout'),
                config('redis.cluster.sentinel.persistent'),
                config('redis.cluster.sentinel.retry_interval'),
                config('redis.cluster.sentinel.read_timeout'),
            );

            $master = $sentinel->master(config("redis.connections.$this->connection.name"));
            if ($master === false) {
                throw new Exception("redis sentinel master is not available ($this->connection)");
            }

            $host = $master['ip'];
            $port = $master['port'];
        } else {
            $host = config("redis.connections.$this->connection.host");
            $port = config("redis.connections.$this->connection.port");
        }

        if (empty($host)) {
            throw new Exception("redis host is not defined ($this->connection)");
        }

        $database = config("redis.connections.$this->connection.database");
        $password = config("redis.connections.$this->connection.password");

        $this->connectionInstance = new Redis();
        $connected = $this->connectionInstance->connect($host, $port);

        if ($connected && !empty($password)) {
            $connected = $this->connectionInstance->auth($password);
        }

        if ($connected === false) {
            throw new Exception("cannot connect to redis ($this->connection)");
        }

        $this->connectionInstance->select($database);
    }

    /**
     * @param string $key
     * @param $value
     * @param int $ttl
     * @throws Exception
     */
    public function psetex(string $key, int $ttl, $value) {
        $result = $this->connectionInstance->psetex($key, $ttl, json_encode($value, JSON_UNESCAPED_UNICODE));
        if ($result !== true) {
            throw new Exception('Failed to perform psetex');
        }
    }

    /**
     * @param string $key
     * @param $value
     * @param int $ttl
     * @throws Exception
     */
    public function setex(string $key, int $ttl, $value) {
        $result = $this->connectionInstance->setex($key, $ttl, json_encode($value, JSON_UNESCAPED_UNICODE));
        if ($result !== true) {
            throw new Exception('Failed to perform setex');
        }
    }

    /**
     * @param string $key
     * @return bool|int
     */
    public function ttl(string $key) {
        return $this->connectionInstance->ttl($key);
    }

    /**
     * @param string $key
     * @return bool|int
     */
    public function pttl(string $key) {
        return $this->connectionInstance->pttl($key);
    }

    /**
     * @param $key string
     * @param $value
     * @throws Exception
     */
    public function set(string $key, $value) {
        $result = $this->connectionInstance->set($key, json_encode($value, JSON_UNESCAPED_UNICODE));
        if ($result !== true) {
            throw new Exception('Failed to perform set');
        }
    }

    /**
     * @param array $array
     * @throws Exception
     */
    public function mset(array $array) {
        $result = $this->connectionInstance->mset($array);
        if ($result !== true) {
            throw new Exception('Failed to perform mset');
        }
    }

    /**
     * Достает данные из кеша
     *
     * @param string $key
     * @param bool $associative - достать данные как ассоциативный массив или объект
     * @return mixed|null
     */
    public function get(string $key, bool $associative = false) {
        $data = $this->connectionInstance->get($key);
        if (empty($data)) {
            return null;
        }
        return json_decode($data, $associative);
    }

    /**
     * @param string[] $keys
     * @return array
     * @throws Exception
     */
    public function mget($keys): array {
        $data = $this->connectionInstance->mget($keys);
        if ($data === false) {
            throw new Exception('Failed to perform mget');
        }
        if (!empty($data)) {
            return array_map(
                function ($dataCached) {
                    return !empty($dataCached) ? json_decode($dataCached) : null;
                },
                $data
            );
        }
        return $data;
    }

    /**
     * @param string $key
     * @param string $hashKey
     * @param $value
     * @return int
     * @throws Exception
     */
    public function hset(string $key, string $hashKey, $value) {
        $result = $this->connectionInstance->hSet($key, $hashKey, json_encode($value, JSON_UNESCAPED_UNICODE));
        if ($result === false) {
            throw new Exception('Failed to perform hset');
        }
        return $result;
    }

    /**
     * @param string $key
     * @param string $hashKey
     * @return mixed|null
     */
    public function hget(string $key, string $hashKey) {
        $data = $this->connectionInstance->hGet($key, $hashKey);
        if (empty($data)) {
            return null;
        }
        return json_decode($data);
    }

    /**
     * Увеличивает значение, хранимое по ключу, на 1
     * Если ключ не существует, то будет проставлено значение 1
     *
     * @param string $key
     * @return int
     */
    public function incr(string $key): int
    {
        return $this->connectionInstance->incr($key);
    }

    /**
     * Записать таблицу значений
     *
     * @param string $key
     * @param array $data Ассоциативный массив ключ-значение, где каждый ключ выступает в роли field
     *
     * @return bool
     * @throws Exception
     */
    public function hmset(string $key, array $data): bool
    {
        $result = $this->connectionInstance->hMSet(
            $key,
            array_map(fn($value) => json_encode($value, JSON_UNESCAPED_UNICODE), $data)
        );

        if ($result === false) {
            throw new RuntimeException('Failed to perform hMSet');
        }

        return true;
    }

    /**
     * Получить записи из таблицы по ключам
     *
     * @param string $key Ключ, по которому взять значения
     * @param string[] $fields Поля, по которым надо извлечь данные
     *
     * @return mixed[]
     */
    public function hmget(string $key, array $fields): array
    {
        return array_map(
            fn($value) => $value === false ? null : json_decode($value),
            $this->connectionInstance->hMGet($key, $fields)
        );
    }

    /**
     * Получить все записи из таблицы по ключу
     *
     * @param string $key Ключ, по которому взять значения
     *
     * @return mixed[]
     */
    public function hgetall(string $key): array
    {
        return array_map(
            fn($value) => json_decode($value),
            $this->connectionInstance->hGetAll($key)
        );
    }

    /**
     * Выполнить операции в одной транзакции
     *
     * @param callable $transactionFunction Функция для определения выполнения операций.
     *      Принимает на вход экземпляр класса Redis с включенном режимом транзакционности
     *      (каждый вызов метода возвращает текущий экземпляр Redis).
     *      Функция ничего не возвращает.
     * @param int $mode Режим запуска multi
     *
     * @return array Ассоциативный массив результатов выполненных операций
     */
    public function multi(callable $transactionFunction, int $mode = Redis::MULTI): array
    {
        $multiRedis = $this->connectionInstance->multi($mode);
        $transactionFunction($multiRedis);

        return $multiRedis->exec();
    }

    /**
     * @param string $key
     * @return bool|int
     */
    public function exists(string $key) {
        return $this->connectionInstance->exists($key);
    }

    /**
     * @param string $key
     * @param int $ttl
     * @return bool
     * @throws Exception
     */
    public function expire(string $key, int $ttl): bool {
        $result = $this->connectionInstance->expire($key, $ttl);
        if ($result === false) {
            throw new Exception('Failed to perform expire');
        }
        return $result;
    }

    /**
     * @param string $key
     * @param int $score
     * @param $value
     * @return int
     */
    public function zadd(string $key, int $score, $value): int {
        return $this->connectionInstance->zAdd($key, ['CH'], $score, json_encode($value, JSON_UNESCAPED_UNICODE));
    }

    /**
     * @param string $key
     * @param int $start
     * @param int $end
     * @param bool|null $withScores
     * @return array
     */
    public function zrange(string $key, int $start, int $end, bool $withScores = null): array {
        return $this->connectionInstance->zRange($key, $start, $end, $withScores);
    }

    /**
     * @param string $key
     * @param int $start
     * @param int $end
     * @param bool|null $withScores
     * @return array
     */
    public function zrevrange(string $key, int $start, int $end, bool $withScores = null): array {
        return $this->connectionInstance->zRevRange($key, $start, $end, $withScores);
    }

    /**
     * Возвращает число элементов в отсортированном сете, лежащем по переданному по ключу
     * со score между start и end. Добавление скобки перед start или end исключает это значение
     * из range. +inf и -inf - тоже валидные лимиты.
     *
     * @param string $key
     * @param string $start
     * @param string $end
     * @return int
     */
    public function zcount(string $key, string $start, string $end): int
    {
        return $this->connectionInstance->zCount($key, $start, $end);
    }

    /**
     * @param string $key
     * @param int $start
     * @param int $end
     * @param int|null $offset
     * @param int|null $limit
     * @param bool|null $withScores
     * @return array
     */
    public function zrangebyscore(string $key, $start = -INF, $end = INF, int $offset = null, int $limit = null, bool $withScores = null): array {
        $options = [];
        if (!is_null($withScores)) {
            $options['withscores'] = $withScores;
        }
        if (!is_null($offset) && !is_null($limit)) {
            $options['limit'] = ['offset' => $offset, 'count' => $limit];
        }
        $startParam = is_infinite($start) ? '-inf' : $start;
        $endParam = is_infinite($end) ? '+inf' : $end;
        return $this->connectionInstance->zRangeByScore($key, $startParam , $endParam, $options);
    }

    /**
     * @param string $key
     * @param int $start
     * @param int $end
     * @param int|null $offset
     * @param int|null $limit
     * @param bool|null $withScores
     * @return array
     */
    public function zrevrangebyscore(string $key, $start = INF, $end = -INF, $offset = null, $limit = null, bool $withScores = null): array {
        $options = [];
        if (!is_null($withScores)) {
            $options['withscores'] = $withScores;
        }
        if (!is_null($offset) && !is_null($limit)) {
            $options['limit'] = [$offset, $limit];
        }
        $startParam = is_infinite($start) ? '+inf' : $start;
        $endParam = is_infinite($end) ? '-inf' : $end;
        return $this->connectionInstance->zRevRangeByScore($key, $startParam , $endParam, $options);
    }

    /**
     * @param string $resultKey
     * @param array $keys
     * @param array|null $weights
     * @return int
     */
    public function zinterstore(string $resultKey, array $keys, array $weights = null): int {
        return $this->connectionInstance->zInterStore($resultKey, $keys, $weights);
    }

    /**
     * @param string $resultKey
     * @param array $keys
     * @param array|null $weights
     * @return int
     */
    public function zunionstore(string $resultKey, array $keys, array $weights = null): int {
        return $this->connectionInstance->zUnionStore($resultKey, $keys, $weights);
    }

    /**
     * @param array $keys
     * @return int
     */
    public function unlink(array $keys): int {
        return $this->connectionInstance->unlink(...$keys);
    }

    /**
     * @param string $pattern
     * @return Generator|string[]
     */
    public function scan(string $pattern): Generator {
        $iterator = null;
        while ($iterator !== 0) {
            $result = $this->connectionInstance->scan($iterator, $pattern);
            if (!empty($result)) {
                foreach ($result as $key) {
                    yield $key;
                }
            }
        }
    }

    /**
     * Вспомогательный метод для действий над пачками с использованием redis.
     *
     * @deprecated
     * @param Generator $items Итератор сущностей
     * @param callable $cb Функция, принимающая в кач-ве аргументов клиент redis и пачку сущностей
     * @param int $chunkSize Размер пачки
     * @return int
     */
    protected function pipelineByBatch(Generator $items, callable $cb, int $chunkSize = 1000): int {
        $count = 0;

        BatchUtils::batch(
            $items,
            function(array $chunkItems) use ($cb, &$count) {
                $client = $this->connectionInstance->pipeline();
                $client = $cb($client, $chunkItems);
                $count += (int) array_sum($client->exec());
            },
            $chunkSize
        );

        return $count;
    }

    /**
     * Вспомогательная функция для постраничного формирования redis pipeline
     *
     * Используется для оптимизации производительности.
     * Итерирует сущности, формирует пачки с указанным размером
     * и помещает операции редис для данной пачки в pipeline
     *
     * @param Generator $items
     * @param callable $cb Коллбэк Example: function (Redis $client, mixed $item, int $index): Redis {return $client}
     * @param int $chunkSize
     * @return int
     */
    protected function pipeline(Generator $items, callable $cb, int $chunkSize = 1000): int {
        $index = 0;

        $batchCallback = function ($client, $chunkItems) use ($cb, &$index) {
            foreach ($chunkItems as $item) {
                $client = $cb($client, $item, $index++);
            }
            return $client;
        };

        return $this->pipelineByBatch($items, $batchCallback, $chunkSize);
    }

    /**
     * Очистка кеша поиска по шаблону ключа
     *
     * Используется неблокирующий метод unlink
     * Каждая пачка из N ключей помещается в одну команду unlink
     *
     * @param string $pattern
     * @param int $clearChunkSize Количество сущностей для очистки в одну итерацию
     * @return int
     */
    protected function unlinkByPattern(string $pattern, int $clearChunkSize = 1000): int {
        $count = 0;
        $keyIterator = $this->scan($pattern);
        $chunks = [];
        $chunkSize = 0;
        while ($keyIterator->valid()) {
            $chunks[] = $keyIterator->current();
            $keyIterator->next();
            if (++$chunkSize >= $clearChunkSize || !$keyIterator->valid()) {
                $count += $this->unlink($chunks);
                $chunkSize = 0;
                $chunks = [];
            }
        }
        return $count;
    }

    /**
     * Экспирация кеша поиска по шаблону ключа
     *
     * @param string $pattern
     * @param int $ttl
     * @return int
     * @throws Exception
     */
    protected function expireByPattern(string $pattern, int $ttl = 0): int
    {
        $count = 0;
        $keyIterator = $this->scan($pattern);
        while ($keyIterator->valid()) {
            $this->expire($keyIterator->current(), $ttl);
            $count += 1;
            $keyIterator->next();
        }
        return $count;
    }

    /**
     * Удаляет записи по ключам
     *
     * @param int|string|array $key
     * @return int
     */
    public function del($key): int
    {
        return $this->connectionInstance->del($key);
    }

    public function exec()
    {
        return $this->connectionInstance->exec();
    }

    public function eval(string $script, $args = [], $numKeys = 0)
    {
        return $this->connectionInstance->eval($script, $args, $numKeys);
    }
}
