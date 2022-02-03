<?php

namespace Common\Base\Repositories\Redis;

use Exception;
use Generator;

use Common\Base\Entities\SerializableTrait;

/**
 * Trait RedisSortingCacheTrait
 * @package Common\Repositories\Traits
 *
 * Трейт для создания кэша сущностей репозитория, а так же индексов фильрованных и сортированных списков
 *
 * Мы можем хранить в базе redis сортированные списки значений, в нашем случае мы будем хранить идентификаторы сущностей.
 *
 * Базовый список содержит идентификаторы всех сущностей, отсортрированный по возрастанию
 * значения идентификатора (считаем это сортировкой по умолчанию).
 *
 * Каждая сущность хранится в сериализованном виде.
 *
 * Для реализации фильтрации и сортировки используется возможность redis объединять и получать
 * пересечение сортированных списков.
 *
 * Индекс фильтров состоит из списков идентификаторов сущностей, сохраненных в redis по ключу,
 * содержащему информацию о поле и значении, по которому данные сущности были отобраны
 * (это либо непосредственно поле сущности, либо вычисляемое значение)
 *
 * Индекс сортировки аналогично содержит сортированный по признаку
 * (полю сущности или вычисленному значению) в порядке возрастания.
 * Сортированные списки в redis можно получить в инвертированном виде.
 *
 * Результат запроса формируется путем пересечения и объединения (в случае использования нескольких фильтров
 * по одному и тому же полю) базового списка идентификаторов и требуемых индексов.
 * Финальный отсортированный список содержит необходимые идентификаторы.
 * На основании данного списка формируется итератор, возвращающий десериализованное значение сущностей из redis.
 *
 * Ограничения:
 * Применим для репозиториев с целочисленными идентификаторами сущностей
 * Поддерживается сортировка извлекаемого кэша единовременно только по одному параметру
 *
 * @var string $connection Ключ конфигурации redis
 *
 * @var string $entityKeyPrefix Префикс ключей на уровне репозитория. Example: "instrument:"
 * @var string $entityIdField Поле с индентификатором сущности (по умолчанию "id")
 * @var bool $sortByEntityIdField Признак сортировки сущностей по идентификатору по умолчанию (по умолчанию false)
 *      Использовать, только если идентификатор сущности int
 * @var string $entityCalculatedFields Ассоциативный массив с названием поля и функцией вычисления значения,
 *      которые будут добавлены в сериализованный объект кэша сущности
 *
 * @var int|null $cacheTtl Время жизни ключей кэша.
 *      В случае, если параметр не задан, ключи сохраняются перманентно
 * @var int|null $resultCacheTtl Опциональный параметр. Время жизни результата выборки из кеша.
 *      В отсутствии будет применен $cacheTtl или 10 секунд
 *
 * Параметризации фильтров и сортировки описаны в RedisRepositoryCacheFilter и RedisRepositoryCacheOrder
 */
trait RedisRepositoryCacheTrait
{
    use RedisRepositoryTrait;
    use RedisRepositoryCacheFilter;
    use RedisRepositoryCacheOrder;
    use SerializableTrait;

    /**
     * Обновление кэша сущностей
     *
     * Создает список id сущностей,
     * а также сохраняет все сериализованные объекты сущностей
     *
     * @param Generator $items
     * @param int|null $chunkSize
     * @param callable|null $processCallback Коллбэк
     * @return int
     * @throws Exception
     */
    public function prepareEntitiesCache(Generator $items, int $chunkSize = null, callable $processCallback = null)
    {
        $entitiesKey = $this->getKeyEntities();
        $entityIds = [];
        $entityIdField = $this->getEntityIdField();
        $cacheTtl = $this->getCacheTtl();
        $count = $this->pipelineByBatch(
            $items,
            function ($client, $items) use ($entityIdField, $cacheTtl, $entitiesKey, &$entityIds, $processCallback) {
                if (!is_null($processCallback)) {
                    $processCallback($items);
                }
                $itemsToSet = [];
                foreach ($items as $item) {
                    $entityId = $item[$entityIdField];
                    $entityIds[] = $entityId;
                    foreach ($this->entityCalculatedFields ?? [] as $calculatedField => $calcFunction) {
                        if (is_callable($calcFunction)) {
                            $value = $calcFunction($item);
                            $item[$calculatedField] = $value;
                        }
                    }
                    $itemsToSet[$entityId] = json_encode($item, JSON_UNESCAPED_UNICODE);
                }
                $client->hmset($this->getKeyEntity(), $itemsToSet);
                return $client;
            },
            $chunkSize
        );
        if (!is_null($cacheTtl)) {
            $this->expire($this->getKeyEntity(), $cacheTtl);
        }
        $entityWeight = $this->isSortByEntityIdField() ? 'entityId' : 0;

        $script = '
            for _, entityId in ipairs(ARGV) do
                redis.call("ZADD", KEYS[1], ' . $entityWeight . ', entityId)
            end
        ';
        $this->connectionInstance->eval($script, array_merge([$entitiesKey], $entityIds), 1);
        if (!is_null($cacheTtl)) {
            $this->expire($entitiesKey, $cacheTtl);
        }
        return $count;
    }

    /**
     * Очистка кэша и индексов
     *
     * @param int $cacheClearingChunkSize Количество сущностей для очистки в одну итерацию
     * @return int
     */
    public function clearCache(int $cacheClearingChunkSize): int
    {
        $count = 0;
        $count += $this->clearEntitiesCache($cacheClearingChunkSize);
        $count += $this->clearFilterCache($cacheClearingChunkSize);
        $count += $this->clearOrderCache($cacheClearingChunkSize);
        return $count;
    }

    /**
     * Очистка кэша сущностей
     *
     * @param int $cacheClearingChunkSize Количество сущностей для очистки в одну итерацию
     * @return int
     */
    public function clearEntitiesCache(int $cacheClearingChunkSize): int
    {
        $count = 0;
        $count += $this->unlinkByPattern($this->getKeyEntity(), $cacheClearingChunkSize);
        $count += $this->unlinkByPattern($this->getEntityKeyPrefix() . ':entity:*', $cacheClearingChunkSize);
        $count += $this->unlinkByPattern($this->getKeyEntities(), $cacheClearingChunkSize);
        return $count;
    }

    /**
     * Получение сущности по идентификатору из кэша
     *
     * @param int|string $entityId
     * @param array|string[] $fields
     * @return array|null
     */
    public function getCachedEntityById($entityId, array $fields = []): ?array
    {
        return $this->applyFilterFields(
            (array)$this->hget($this->getKeyEntity(), $entityId),
            $fields
        );
    }

    /**
     * Получение коллекции сущностей из кэша
     *
     * Возвращает отфильтрованный и отсортированный срез сущностей с фильтром полей каждой сущности.
     *
     * Внимание! Трейт имеет ограничения сортировки. Будет использован только первый параметр сортировки!
     *
     * @param array $filters Список фильтров
     * @param float $limit Лимит списка
     * @param int $offset Смещение, начиная с которого необходимо вернуть список
     * @param array $orderBy Список сортировок (для сортировки будет использован только первый элемент массива)
     * @param array $fields Список полей, которые должны содержать возвращаемые сущности
     * @return Generator
     * @throws Exception
     */
    public function getCachedCollection(
        array $filters = [],
        float $limit = INF,
        int $offset = 0,
        array $orderBy = [],
        array $fields = ['*']
    ): Generator {
        [$filteredIds, $resultKey] = $this->getFilteredAndOrderedItemsResultKey($filters, $orderBy);

        $orderByParam = !empty($orderBy) ? $orderBy[0] : null;
        $orderDirection = !is_null($orderByParam) && count($orderByParam) === 2 ? $orderByParam[1] : 'asc';

        $start = $offset;
        $end = $limit === INF ? -1 : ($offset + $limit - 1);

        $entityIds = $orderDirection === 'asc'
            ? $this->zrange($resultKey, $start, $end)
            : $this->zrevrange($resultKey, $start, $end);

        $filteredEntityIds = !empty($filteredIds)
            ? array_filter(
                $entityIds,
                fn($entityId) => in_array($entityId, $filteredIds)
            )
            : $entityIds;

        if (empty($filteredEntityIds)) {
            return [];
        }

        $entities = $this->hmget($this->getKeyEntity(), $filteredEntityIds);

        foreach ($entities as $entity) {
            yield $this->applyFilterFields((array)$entity, $fields);
        }
    }

    /**
     * Возвращает число штук в кеше по фильтрам и сортировке
     * (сортировка передается, чтоб не пересчитывался результат, если он есть в кеше)
     *
     * @param array $filters
     * @param array $orderBy
     * @return int
     * @throws Exception
     */
    public function getCount(
        array $filters = [],
        array $orderBy = []
    ): int {
        [$filteredIds, $resultKey] = $this->getFilteredAndOrderedItemsResultKey($filters, $orderBy);
        return $this->zcount($resultKey, '-inf', '+inf');
    }

    /**
     * Метод получения ключа, по которому будет сохранена сериализованная сущность
     *
     * @return string
     */
    protected function getKeyEntity(): string
    {
        return $this->getEntityKeyPrefix() . ':entity';
    }

    /**
     * Метод получения префикса сущности кеша
     *
     * @return string
     */
    protected function getEntityKeyPrefix(): string
    {
        return $this->entityKeyPrefix ?? '';
    }

    /**
     * Метод получения поля идентификатора сущности
     *
     * @return string
     */
    protected function getEntityIdField(): string
    {
        return $this->entityIdField ?? 'id';
    }

    /**
     * Метод получения ttl сущностей кеша
     * @return int|null
     */
    protected function getCacheTtl(): ?int
    {
        return $this->cacheTtl ?? null;
    }

    /**
     * Метод получения признака необходимости сортировать сущности по идентификатору
     *
     * @return bool
     */
    protected function isSortByEntityIdField(): bool
    {
        return $this->sortByEntityIdField ?? false;
    }

    /**
     * Метод получения ключа, по которому будет сохранен полный список сущностей
     *
     * @return string
     */
    protected function getKeyEntities(): string
    {
        return $this->getEntityKeyPrefix() . ':entities:_all_';
    }

    /**
     * Метод получения ttl результата выборки из кеша
     * @return int
     */
    protected function getResultCacheTtl(): int
    {
        return $this->resultCacheTtl ?? $this->getCacheTtl() ?? 10;
    }

    /**
     * Применение фильтра полей к возвращаемой сущности
     *
     * @param array $entity
     * @param array $fields
     * @return array|null
     */
    private function applyFilterFields(array $entity, array $fields = []): ?array
    {
        if (empty($fields) || $fields[0] === '*' || empty($entity)) {
            return $entity;
        }
        return array_filter(
            $entity,
            fn($key) => in_array($key, $fields, true),
            ARRAY_FILTER_USE_KEY
        );
    }

    /**
     * @param array $filters
     * @param array $orderBy
     * @return string
     * @throws Exception
     */
    private function getFilteredAndOrderedItemsResultKey(
        array $filters = [],
        array $orderBy = []
    ): array {
        $filteredIds = [];

        $resultIntersectKeys = [$this->getKeyEntities()];
        $resultIntersectWeight = [0];

        $orderByParam = $orderBy[0] ?? null;

        $resultKey = $this->getEntityKeyPrefix() . ':_result_:' . json_encode([$filters, $orderBy]);

        if (!$this->exists($resultKey)) {
            $client = $this->connectionInstance->pipeline();

            $this->applyFilters($client, $filters, $resultIntersectKeys, $resultIntersectWeight, $filteredIds);

            $this->applyOrderByParams($orderByParam, $resultIntersectKeys, $resultIntersectWeight);

            $client = $client->zinterstore($resultKey, $resultIntersectKeys, $resultIntersectWeight);
            $client = $client->expire($resultKey, $this->getResultCacheTtl());

            $client->exec();
        }
        return [$filteredIds, $resultKey];
    }
}
