<?php

namespace Common\Base\Repositories\Redis;

use Exception;
use Generator;

/**
 * Trait RedisRepositoryCacheFilter
 * @package Common\Repositories\Traits
 *
 * Трейт-выноска логики создания и применения индексов фильтров к сущностям RedisSortingCacheTrait
 *
 * @var string|null $entityFilterKeyPrefix Опциональный префикс ключей фильтров.
 *      Если его нет, будет выбран основной префикс сущности $entityKeyPrefix
 *      Example: "instrument:bonds"
 *
 * @var int|null $filterTtl Опциональный параметр. Время жизни индексов фильтров.
 *      В отсутствии будет применен $cacheTtl
 * @var int|null $tmpFilterTtl Опциональный параметр. Время жизни временных индексов фильтров.
 *      В отсутствии будет применен $cacheTtl
 *
 * @var array $filterFields Список полей для кэша фильтров. Example: ['filterField1', 'filterField2', ...]
 * @var array $calculatedFilterFields Ассоциативный массив функций для кэша с вычисляемыми значениями фильтров.
 *      Функция должна возвращать строковое значение.
 *      Example: ['filterField' => function (array $entity) {return 'calculatedValue'}, ...]
 * @var array $comparisonFilterFields Список полей для кэша фильтров с операцией сравнения значений.
 *      Функция должна возвращать числовое значение.
 *      Example: ['filterField' => function (array $entity) {return 0.01}, ...]
 */
trait RedisRepositoryCacheFilter
{

    /**
     * Обновление индекса фильтров
     *
     * @param Generator $items Итератор сущностей
     * @param array|null $filterFields
     * @param callable|null $processCallback
     * @return int
     */
    public function prepareFilterCache(
        Generator $items,
        ?array $filterFields = null,
        ?callable $processCallback = null
    ): int {
        $entityIdField = $this->getEntityIdField();
        $filterFields = empty($filterFields) ? $this->getFilterFields() : $filterFields;
        $calculatedFilterFields = $this->getCalculatedFilterFields();
        $comparisonFilterFields = $this->getComparisonFilterFields();

        $filterDataMap = [];

        foreach ($items as $item) {
            foreach ($filterFields as $filterField) {
                $filterValue = $this->getFilterValueAsString($item[$filterField]);
                $key = $this->getFilterKey($filterField, $filterValue);
                $filterDataMap[$key][] = 0;
                $filterDataMap[$key][] = $item[$entityIdField];
            }

            foreach (array_keys($calculatedFilterFields) as $filterField) {
                $calculatedFilterValue = $calculatedFilterFields[$filterField]($item);
                if (!is_null($calculatedFilterValue)) {
                    $key = $this->getFilterCalculatedKey($filterField, $calculatedFilterValue);
                    $filterDataMap[$key][] = 0;
                    $filterDataMap[$key][] = $item[$entityIdField];
                }
            }

            foreach (array_keys($comparisonFilterFields) as $filterField) {
                $score = $comparisonFilterFields[$filterField]($item);
                $key = $this->getFilterComparisonKey($filterField);
                if (!is_null($score)) {
                    $filterDataMap[$key][] = $score;
                    $filterDataMap[$key][] = $item[$entityIdField];
                }
            }
            if (!empty($processCallback)) {
                $processCallback($item);
            }
        }

        $client = $this->connectionInstance->pipeline();

        foreach ($filterDataMap as $key => $values) {
            $script = '
                redis.call("DEL", KEYS[1])
                local count = redis.call("ZADD", KEYS[1], unpack(ARGV))
                redis.call("EXPIRE", KEYS[1], KEYS[2])
                return count
            ';
            $client = $client->eval($script, array_merge([$key, $this->getFilterTtl()], $values), 2);
        }
        return array_sum($client->exec());
    }

    /**
     * Метод для получения полей фильтрации репозитория
     *
     * @return string[]
     */
    protected function getFilterFields(): array
    {
        return $this->filterFields ?? [];
    }

    /**
     * Метод для получения вычисляемых полей для фильтрации
     *
     * @return string[]
     */
    protected function getCalculatedFilterFields(): array
    {
        return $this->calculatedFilterFields ?? [];
    }

    /**
     * Метод для получения полей для сравнения
     *
     * @return string[]
     */
    protected function getComparisonFilterFields(): array
    {
        return $this->comparisonFilterFields ?? [];
    }

    /**
     * Получение строкового представления значения фильтра для использования его при формировании ключа,
     * по которому будет хранится данный индекс
     *
     * @param $filterValue
     * @return string
     */
    protected function getFilterValueAsString($filterValue): string
    {
        if (!is_string($filterValue)) {
            return json_encode($filterValue, JSON_UNESCAPED_UNICODE);
        }
        return $filterValue;
    }

    /**
     * Метод получения ключа, по которому будет сохранен индекс фильтра
     *
     * @param string $key Поле фильтра
     * @param string|null $value Значение фильтра
     * @return string
     */
    protected function getFilterKey(string $key, string $value = null): string
    {
        return $this->entityFilterKeyPrefix() . ':filterBy:' . $key . (is_null($value) ? '' : '__' . $value);
    }

    /**
     * Метод получения ключа, по которому будет сохранен индекс фильтра по вычисленному полю
     *
     * @param string $key
     * @param string $value
     * @return string
     */
    protected function getFilterCalculatedKey(string $key, string $value): string
    {
        return $this->entityFilterKeyPrefix() . ':filterBy:_calculated_:' . $key . '__' . $value;
    }

    /**
     * Метод получения ключа, по которому будет сохранен индекс фильтра методом сравнения
     *
     * @param string $key
     * @return string
     */
    protected function getFilterComparisonKey(string $key): string
    {
        return $this->entityFilterKeyPrefix() . ':filterBy:_comparison_:' . $key;
    }

    /**
     * Метод для получения ttl индексов фильтров.
     * @return int
     */
    protected function getFilterTtl(): int
    {
        return $this->filterTtl ?? $this->getCacheTtl();
    }

    /**
     * Очистка индекса фильтров
     *
     * @param int $cacheClearingChunkSize Количество сущностей для очистки в одну итерацию
     * @return int
     */
    public function clearFilterCache(int $cacheClearingChunkSize): int
    {
        return $this->unlinkByPattern($this->getFilterKey('*'), $cacheClearingChunkSize);
    }

    /**
     * Метод применения фильтров
     *
     * @param $client
     * @param array $filters
     * @param array $resultIntersectKeys
     * @param array $resultIntersectWeight
     * @param array $filteredIds
     * @throws Exception
     */
    protected function applyFilters(
        &$client,
        array $filters,
        array &$resultIntersectKeys,
        array &$resultIntersectWeight,
        array &$filteredIds
    ) {
        foreach ($filters as $filter) {
            if ($this->isFilterByEntityId($filter)) {
                $this->applyFilterByEntityId(
                    $client,
                    $filter,
                    $resultIntersectKeys,
                    $resultIntersectWeight,
                    $filteredIds
                );
                continue;
            }
            if ($this->isExpectedFilter($filter)) {
                if (count($filter) === 3 && $filter[1] === 'in') {
                    $this->applyFilter(
                        $client,
                        $filter,
                        $resultIntersectKeys,
                        $resultIntersectWeight
                    );
                    continue;
                }
                if (count($filter) === 3 && in_array($filter[1], ['>', '<', '>=', '<='])) {
                    $this->applyFilterComparison(
                        $client,
                        $filter,
                        $resultIntersectKeys,
                        $resultIntersectWeight
                    );
                    continue;
                }
                $resultIntersectKeys[] = $this->getKeyByFilter($filter);
                $resultIntersectWeight[] = 0;
                continue;
            }
        }
    }

    /**
     * Метод проверки, что фильтр является выборокой по указанным идентификаторам сущностей
     *
     * @param array $filter
     * @return bool
     */
    protected function isFilterByEntityId(array $filter): bool
    {
        return count($filter) >= 2 && $filter[0] === $this->getEntityIdField();
    }

    /**
     * Метод применения фильтра по id
     *
     * @param $client
     * @param array $filter
     * @param array $resultIntersectKeys
     * @param array $resultIntersectWeight
     * @param array $filteredIds
     */
    protected function applyFilterByEntityId(
        &$client,
        array $filter,
        array &$resultIntersectKeys,
        array &$resultIntersectWeight,
        array &$filteredIds
    ) {
        if (count($filter) === 2) {
            $filteredIds[] = $filter[1];
        } else {
            if (count($filter) === 3 && $filter[1] === '=') {
                $filteredIds[] = $filter[2];
            } else {
                if (count($filter) === 3 && $filter[1] === 'in') {
                    $filteredIds = $filter[2];
                }
            }
        }
        if (!empty($filteredIds)) {
            $filterIdTmpKey = $this->getTmpFilterKey($filter);
            $resultIntersectKeys[] = $filterIdTmpKey;
            $resultIntersectWeight[] = 0;
            $client = $client->eval(
                '
                if redis.call("EXISTS", KEYS[1]) == 0 then
                    for _, filteredId in ipairs(ARGV) do
                        redis.call("ZADD", KEYS[1], 0, filteredId)
                    end
                    redis.call("EXPIRE", KEYS[1], KEYS[2])
                end
            ',
                array_merge([$filterIdTmpKey, $this->getTmpFilterTtl()], $filteredIds),
                2
            );
        }
    }

    /**
     * Метод получения ключа, по которому будет сохранен временный отфильтрованный список идентификаторов
     *
     * @param array $filter
     * @return string
     */
    protected function getTmpFilterKey(array $filter): string
    {
        return $this->entityFilterKeyPrefix() . ':filterBy:_tmp_:' . json_encode($filter, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Метод для получения префикса ключа фильтра
     * @return string
     */
    protected function entityFilterKeyPrefix(): string
    {
        return $this->entityFilterKeyPrefix ?? $this->getEntityKeyPrefix();
    }

    /**
     * Метод для получения ttl временных индексов фильтров.
     * @return int
     */
    protected function getTmpFilterTtl(): int
    {
        return $this->tmpFilterTtl ?? $this->getCacheTtl();
    }

    /**
     * Метод проверки фильтра
     *
     * Проверяет, предусмотрен ли для данного фильтра предустановленный индекс
     *
     * @param array $filter
     * @return bool
     */
    protected function isExpectedFilter(array $filter): bool
    {
        if (empty($filter) || count($filter) < 2 || count($filter) > 3) {
            return false;
        }

        $filterName = $this->getFilterFieldName($filter[0]);

        if (
            !in_array($filterName, $this->getFilterFields()) &&
            !array_key_exists($filterName, $this->getCalculatedFilterFields()) &&
            !array_key_exists($filterName, $this->getComparisonFilterFields())
        ) {
            return false;
        }
        if (
            count($filter) === 3 &&
            (in_array($filterName, $this->getFilterFields()) ||
                array_key_exists($filterName, $this->getCalculatedFilterFields())) &&
            !in_array($filter[1], ['=', 'in'])
        ) {
            return false;
        }
        if (count($filter) === 3 && array_key_exists($filterName, $this->getComparisonFilterFields()) &&
            !in_array($filter[1], ['<', '>', '<=', '>='])) {
            return false;
        }
        return true;
    }

    /**
     * Метод приведения наименования фильтра к единообразному виду
     *
     * @param string $filterParam
     * @return string
     */
    protected function getFilterFieldName(string $filterParam): string
    {
        $parts = explode('.', $filterParam);
        if (count($parts) === 1) {
            return $parts[0];
        }
        if (count($parts) === 2) {
            return $parts[1];
        }
        return '';
    }

    /**
     * Метод применения фильтра по полю
     *
     * @param $client
     * @param array $filter
     * @param array $resultIntersectKeys
     * @param array $resultIntersectWeight
     * @throws Exception
     */
    protected function applyFilter(
        &$client,
        array $filter,
        array &$resultIntersectKeys,
        array &$resultIntersectWeight
    ) {
        $filterUnionKeys = [];
        foreach ($filter[2] as $value) {
            $filterUnionKeys[] = $this->getKeyByFilter([$filter[0], $value]);
        }
        if (!empty($filterUnionKeys)) {
            $filterUnionResultTmp = $this->getTmpFilterKey($filter);
            $resultIntersectKeys[] = $filterUnionResultTmp;
            $resultIntersectWeight[] = 0;
            $client = $client->eval(
                '
                if redis.call("EXISTS", KEYS[1]) == 0 then
                    redis.call("ZUNIONSTORE", KEYS[1], #ARGV, unpack(ARGV))
                    redis.call("EXPIRE", KEYS[1], KEYS[2])
                end
            ',
                array_merge([$filterUnionResultTmp, $this->getTmpFilterTtl()], $filterUnionKeys),
                2
            );
        }
    }

    /**
     * Метод получения ключа по массиву фильтра
     *
     * @param array $filter
     * @return string|null
     * @throws Exception
     */
    protected function getKeyByFilter(array $filter): ?string
    {
        if (empty($filter)) {
            return null;
        }
        $filterName = $this->getFilterFieldName($filter[0]);
        if (count($filter) === 2 || count($filter) === 3 && $filter[1] === '') {
            $filterValue = $this->getFilterValueAsString($filter[1]);
            if (in_array($filterName, $this->getFilterFields())) {
                return $this->getFilterKey($filterName, $filterValue);
            }
            if (array_key_exists($filterName, $this->getCalculatedFilterFields())) {
                return $this->getFilterCalculatedKey($filterName, $filterValue);
            }
        }
        if (count($filter) === 3 && $filter[1] === 'in') {
            if (
                in_array($filterName, $this->getFilterFields())
                || array_key_exists($filterName, $this->getCalculatedFilterFields())
            ) {
                throw new Exception('"In" filter must be splitted');
            }
        }
        if (count($filter) === 3 && in_array($filter[1], ['>', '<', '>=', '<='])) {
            if (array_key_exists($filterName, $this->getComparisonFilterFields())) {
                return $this->getFilterComparisonKey($filterName);
            }
        }

        return $this->getRawFilterKey($filter);
    }

    /**
     * Метод получения ключа, по которому будет сохранен индекс фильтра, который не был заранее проиндексирован
     *
     * @param array $filter
     * @return string
     */
    protected function getRawFilterKey(array $filter): string
    {
        return $this->entityFilterKeyPrefix() . ':filterBy:' . json_encode($filter, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Метод применения фильтра сравнения
     *
     * @param $client
     * @param array $filter
     * @param array $resultIntersectKeys
     * @param array $resultIntersectWeight
     */
    protected function applyFilterComparison(
        &$client,
        array $filter,
        array &$resultIntersectKeys,
        array &$resultIntersectWeight
    ) {
        $filterField = $this->getFilterFieldName($filter[0]);
        $filterComparisonKey = $this->getFilterComparisonKey($filterField);
        $filterComparisonResultTmpKey = $this->getTmpFilterKey($filter);
        $resultIntersectKeys[] = $filterComparisonResultTmpKey;
        $resultIntersectWeight[] = 0;
        $start = ($filter[1] === '<' || $filter[1] === '<=')
            ? -INF
            : ($filter[1] === '>' ? ('(' . $filter[2]) : $filter[2]);
        $end = ($filter[1] === '>' || $filter[1] === '>=')
            ? INF
            : ($filter[1] === '<' ? '(' . $filter[2] : $filter[2]);
        $client = $client->eval(
            '
            if redis.call("EXISTS", KEYS[1]) == 0 then
                local filteredIds = redis.call("ZRANGEBYSCORE", KEYS[1], ARGV[1], ARGV[2])
                for _, filteredId in ipairs(filteredIds) do
                    redis.call("ZADD", KEYS[2], 0, filteredId)
                end
                redis.call("EXPIRE", KEYS[2], 10)
            end
        ',
            [$filterComparisonKey, $filterComparisonResultTmpKey, $start, $end],
            2
        );
    }
}
