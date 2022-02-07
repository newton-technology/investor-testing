<?php

namespace Common\Base\Repositories\Redis;

use Generator;

/**
 * Trait RedisRepositoryCacheOrder
 * @package Common\Repositories\Traits
 *
 * Трейт-выноска логики создания и применения индексов сортировки к сущностям RedisSortingCacheTrait
 *
 * @var string|null $entitySortKeyPrefix Опциональный префикс ключей сортировки.
 *      Если его нет, будет выбран основной префикс сущности $entityKeyPrefix
 *      Example: "instrument:bonds"
 *
 * @var array $orderFields Список полей с числовыми значениями для сортировки
 * @var array $orderLexFields Список полей со строковыми значениями для сортировки
 * @var array $calculatedOrderFields Список параметров с вычисляемыми числовыми значениями для сортировки
 * @var array $calculatedOrderLexFields Список параметров с вычисляемыми строковыми значениями для сортировки
 */
trait RedisRepositoryCacheOrder
{

    /**
     * Подготовка индекса сортировки по предустановленным параметрам сортировки
     * по существующим полям сущности и по вычисляемым значениям
     *
     * @param Generator $items
     * @param array|null $orderFields
     * @param callable|null $processCallback
     * @return int
     */
    public function prepareOrderCache(
        Generator $items,
        ?array $orderFields = null,
        ?callable $processCallback = null
    ): int {
        $orderFields = empty($orderFields)
            ? $this->getOrderFields()
            : $orderFields;
        if (empty($orderFields)) {
            return 0;
        }
        $entityIdField = $this->getEntityIdField();
        return $this->pipeline(
            $items,
            function ($client, $item) use ($orderFields, $entityIdField, $processCallback) {
                if (!is_null($processCallback)) {
                    $processCallback($item);
                }
                $entityId = $item[$entityIdField];
                foreach ($orderFields as $orderField) {
                    $orderValue = array_key_exists($orderField, $item) ? $item[$orderField] : null;
                    $key = $this->getOrderKey($orderField);
                    if (!is_null($orderValue) && is_numeric($orderValue)) {
                        $client = $client->zAdd($key, ['CH'], $orderValue, $entityId);
                    }
                }
                $calculatedOrderFields = $this->getCalculatedOrderFields();
                foreach (array_keys($calculatedOrderFields) as $calculatedOrderField) {
                    $calculatedOrderValue = $calculatedOrderFields[$calculatedOrderField]($item);
                    $key = $this->getCalculatedOrderKey($calculatedOrderField);
                    if (is_numeric($calculatedOrderValue)) {
                        $client = $client->zAdd($key, ['CH'], $calculatedOrderValue, $entityId);
                    }
                }
                return $client;
            }
        );
    }

    /**
     * Метод для получения списка полей с числовыми значениями для сортировки
     *
     * @return array
     */
    protected function getOrderFields(): array
    {
        return $this->orderFields ?? [];
    }

    /**
     * Метод для получения ключа сортировки с числовыми значениями
     *
     * @param string $key
     * @return string
     */
    protected function getOrderKey(string $key): string
    {
        return $this->getEntitySortKeyPrefix() . ':orderBy:' . $key;
    }

    /**
     * Метод для получения префикса ключа сортировки
     *
     * @return string
     */
    protected function getEntitySortKeyPrefix(): string
    {
        return $this->entitySortKeyPrefix ?? $this->getEntityKeyPrefix();
    }

    /**
     * Метод для получения списка полей с вычисляемыми числовыми значениями для сортировки
     *
     * @return array
     */
    protected function getCalculatedOrderFields(): array
    {
        return $this->calculatedOrderFields ?? [];
    }

    /**
     * Метод для получения ключа сортировки с вычисляемыми числовыми значениями
     *
     * @param string $key
     * @return string
     */
    protected function getCalculatedOrderKey(string $key): string
    {
        return $this->getEntitySortKeyPrefix() . ':orderBy:_calculated_:' . $key;
    }

    /**
     * Подготовка индекса сортировки по текстовому полю сущности
     *
     * Вносит в индекс сортировку сущностей в том порядке, в котором этот порядок был передан в функцию
     *
     * @param Generator $sortedAscendingItems Итератор сущностей отсортированный согласно указанному параметру сортировки
     * @param string $orderByField Наименоване параметр сортировки
     * @param callable|null $processCallback
     * @return int
     */
    public function prepareOrderLexCache(
        Generator $sortedAscendingItems,
        string $orderByField,
        ?callable $processCallback = null
    ): int {
        $orderName = $orderByField;
        if (!in_array($orderName, $this->getOrderLexFields())) {
            return 0;
        }
        $orderLexKey = $this->getOrderLexKey($orderName);
        $entityIdField = $this->getEntityIdField();
        return $this->pipeline(
            $sortedAscendingItems,
            function ($client, $item, $index) use ($orderName, $orderLexKey, $entityIdField, $processCallback) {
                if (!is_null($processCallback)) {
                    $processCallback($item);
                }
                $entityId = $item[$entityIdField];
                return $client->zAdd($orderLexKey, ['CH'], $index, $entityId);
            }
        );
    }

    /**
     * Метод для получения списка полей со строковыми значениями для сортировки
     *
     * @return array
     */
    protected function getOrderLexFields(): array
    {
        return $this->orderLexFields ?? [];
    }

    /**
     * Метод для получения ключа сортировки со строковыми значениями
     *
     * @param string $key
     * @return string
     */
    protected function getOrderLexKey(string $key): string
    {
        return $this->getEntitySortKeyPrefix() . ':orderBy:_lex_:' . $key;
    }

    /**
     * Подготовка индекса сортировки по вычисляемому текстовому значению
     *
     * @param Generator $items
     * @param array|null $calculatedOrderLexFields
     * @param callable|null $processCallback
     * @return int
     */
    public function prepareCalculatedOrderLexCache(
        Generator $items,
        ?array $calculatedOrderLexFields = null,
        ?callable $processCallback = null
    ): int {
        $count = 0;
        $calculatedOrderLexFields = empty($calculatedOrderLexFields)
            ? $this->getCalculatedOrderLexFields()
            : $calculatedOrderLexFields;

        foreach (array_keys($calculatedOrderLexFields) as $calculatedOrderLexField) {
            $array = iterator_to_array($items);
            $calcFunction = $calculatedOrderLexFields[$calculatedOrderLexField];
            usort(
                $array,
                function ($itemA, $itemB) use ($calcFunction) {
                    $valueA = $calcFunction($itemA);
                    $valueB = $calcFunction($itemB);
                    return strnatcmp($valueA, $valueB);
                }
            );
            $key = $this->getCalculatedOrderLexKey($calculatedOrderLexField);
            $entityIdField = $this->getEntityIdField();
            $count += $this->pipeline(
                $this->arrayToGenerator($array),
                function ($client, $item, $index) use ($key, $entityIdField, $processCallback) {
                    if (!is_null($processCallback)) {
                        $processCallback($item);
                    }
                    $entityId = $item[$entityIdField];
                    return $client->zAdd($key, $index, $entityId);
                }
            );
        }

        return $count;
    }

    /**
     * Метод для получения списка полей с вычисляемыми строковыми значениями для сортировки
     *
     * @return array
     */
    protected function getCalculatedOrderLexFields(): array
    {
        return $this->calculatedOrderLexFields ?? [];
    }

    /**
     * Метод для получения ключа сортировки с вычисляемыми строковыми значениями
     *
     * @param string $key
     * @return string
     */
    protected function getCalculatedOrderLexKey(string $key): string
    {
        return $this->getEntitySortKeyPrefix() . ':orderBy:_calculated_:_lex_:' . $key;
    }

    /**
     * Вспомогательный метод преобразования массива в генератор
     *
     * @param array $array
     * @return Generator
     */
    protected function arrayToGenerator(array $array): Generator
    {
        foreach ($array as $item) {
            yield $item;
        }
    }

    /**
     * Очистка индексов сортировок
     *
     * @param int $cacheClearingChunkSize Количество сущностей для очистки в одну итерацию
     * @return int
     */
    public function clearOrderCache(int $cacheClearingChunkSize): int
    {
        return $this->unlinkByPattern($this->getOrderKey('*'), $cacheClearingChunkSize);
    }

    /**
     * @param $orderByParam
     * @param string[] $resultIntersectKeys
     * @param int[] $resultIntersectWeight
     */
    protected function applyOrderByParams($orderByParam, array &$resultIntersectKeys, array &$resultIntersectWeight)
    {
        if (empty($orderByParam) ||!$this->isExpectedOrder($orderByParam)) {
            return;
        }
        $orderName = $orderByParam[0];
        if (in_array($orderName, $this->getOrderFields())) {
            $resultIntersectKeys[] = $this->getOrderKey($orderName);
            $resultIntersectWeight[] = 1;
            return;
        }
        if (array_key_exists($orderName, $this->getCalculatedOrderFields())) {
            $resultIntersectKeys[] = $this->getCalculatedOrderKey($orderName);
            $resultIntersectWeight[] = 1;
            return;
        }
        if (in_array($orderName, $this->getOrderLexFields())) {
            $resultIntersectKeys[] = $this->getOrderLexKey($orderName);
            $resultIntersectWeight[] = 1;
            return;
        }
        if (array_key_exists($orderName, $this->getCalculatedOrderLexFields())) {
            $resultIntersectKeys[] = $this->getCalculatedOrderLexKey($orderName);
            $resultIntersectWeight[] = 1;
        }
    }

    /**
     * Метод проверки сортировки
     *
     * Проверяет, предусмотрен ли для данной сортировки предустановленный индекс
     *
     * @param $orderBy
     * @return bool
     */
    protected function isExpectedOrder(array $orderBy): bool
    {
        if (count($orderBy) < 1 || count($orderBy) > 2) {
            return false;
        }
        if (count($orderBy) === 2 &&!in_array($orderBy[1], ['asc', 'desc'])) {
            return false;
        }
        $orderName = $orderBy[0];
        return in_array($orderName, $this->getOrderFields()) ||
            in_array($orderName, $this->getOrderLexFields()) ||
            array_key_exists($orderName, $this->getCalculatedOrderFields()) ||
            array_key_exists($orderName, $this->getCalculatedOrderLexFields());
    }
}
