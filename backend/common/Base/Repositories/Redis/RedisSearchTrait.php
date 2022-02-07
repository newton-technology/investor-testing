<?php

namespace Common\Base\Repositories\Redis;

use Generator;

/**
 * Trait RedisSearchTrait
 * @package Common\Repositories\Traits
 *
 * Трейт для создания поискового индекса по репозиторию сущности, индекс создается в redis
 *
 * @var string $connection Ключ конфигурации redis
 * @var string $keyPrefix Префикс ключей на уровне репозитория. Example: "instruments:"
 * @var string $searchIdField Название поля, значение которого будет сохраняться в массивах индекса для индентификации сущности
 * @var array $params Массив параметров для поискового индекса
 * Example:
 * [
 *   'ticker' => [ // Наименование параметра. Если совпадает с наименованием поля и не указана функция для вычисления значения, то будет взято значение данного поля
 *     'value' => function ($item) {return $item['ticker']}, // Необязательное значение в случае, если данный параметр является полем сущности. По умолчанию null
 *     'type' => 'exact', // Тип индекса. Возможные значения: full | substr | exact. Полный с поддержкой опечаток, по подстрокам (без опечаток), полное совпадение. По умолчанию full
 *     'weight' => 10 | function ($item) {return $item['type'] === 'common_stock' ? 10 : 1} // Модификатор веса. Влияет на ранжирование в выдаче. Можно передать число или функцию, которая будет возвращать модификатор. По умолчанию 1
 *     'aliases' => ['sber' => ['сбер']] // Синонимы к значениям. В поисковую выдачу сущность попадет в том числе при поиске по синонимам, с учетом указанных параметров. По умолчанию null
 *     'minQueryLength' => 3 | function ($item) {return strlen($item['ticker']) >= 3 ? 3 : 1} // Минимальная допустимая длина запроса (фактически n-граммы) для данного параметра. По умолчанию 3
 *   ],
 * ]
 * @var array $searchFilterFields Массив названий полей, по которым будет осуществлятся фильтрация
 * @var int $searchResultTtl Время жизни результатов поиска
 */
trait RedisSearchTrait
{
    use RedisRepositoryTrait;

    /**
     * Префикс ключей индекса поиска по полям
     *
     * @var string
     */
    protected $searchKeyPrefix = 'search:';

    /**
     * Префикс ключей результатов поиска
     *
     * @var string
     */
    protected $searchResultKeyPrefix = 'search:result:';

    /**
     * Префикс ключей индекса n-грамм по id cущности
     *
     * @var string
     */
    protected $searchNGramIndexKeyPrefix = 'search:ngramById:';

    /**
     * Префикс ключей фильтров
     *
     * @var string
     */
    protected $searchFilterKeyPrefix = 'search:filter:';

    /**
     * Минимальный вес значимых результатов поиска
     *
     * @var int
     */
    protected $minResultScore = 2;

    /**
     * Функция для извлечения массива слов из строки, а так же добавление в массив синонимов при наличии
     *
     * Строка приводится к нижнему регистру и разбивается на слова,
     * разделенные пробелами и знаками, отличными от букв, цифр, &, _ и -
     *
     * @param string $string
     * @param array $aliases
     * @return array
     */
    protected function getWords(string $string, array $aliases = [], int $minN = 1): array
    {
        $normalizedString = trim(mb_ereg_replace('[^а-яё\w&_-]+', ' ', mb_strtolower($string)));
        $words = mb_split('\s+', $normalizedString);
        $filterCallback = fn($word) => mb_strlen($word) >= $minN;
        $words = array_filter($words, $filterCallback);
        $wordsWithAliases = $words;
        foreach ($words as $word) {
            if (array_key_exists($word, $aliases)) {
                $wordsWithAliases = array_merge($wordsWithAliases, $aliases[$word]);
            }
        }
        foreach ($aliases as $key => $aliasArray) {
            $pattern = "(^{$key}$|\s{$key}\s|^{$key}\s|\s{$key}$)";
            if (mb_ereg($pattern, $normalizedString)) {
                $wordsWithAliases = array_merge($wordsWithAliases, $aliasArray);
            }
        }
        return $wordsWithAliases;
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
    protected function clearSearchIndexByPattern(string $pattern, int $clearChunkSize = 1000): int
    {
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

    public function addParam(string $name, array $param)
    {
        $this->params[$name] = $param;
    }

    /**
     * Извлечение массива n-грамм из строки
     *
     * @param string $string
     * @param array $aliases
     * @return array
     */
    public function getNGrams(string $string, array $aliases = [], int $minN = 1): array
    {
        $nGrams = [];
        foreach ($this->getWords($string, $aliases, $minN) as $word) {
            for ($i = 0; $i <= mb_strlen($word) - $minN; $i++) {
                for ($j = $minN; $j <= mb_strlen($word) - $i; $j++) {
                    $nGrams[] = mb_substr($word, $i, $j);
                }
            }
        }
        return $nGrams;
    }

    /**
     * Извлечение дополнительных n-грамм из строки
     *
     * Данные n-граммы содержат дополнительные или заменяющие символы _,
     * Добавление данных n-грамм в поисковый индекс позволяет при поиске допускать опечатки следующего типа:
     * замена, пропуск, перестановка символов, а так же лишний символ
     *
     * @param string $string
     * @param array $aliases
     * @return array
     */
    public function getExtraNGrams(string $string, array $aliases = [], int $minN = 1): array
    {
        $extraNGrams = [];
        foreach ($this->getNGrams($string, $aliases, $minN) as $nGram) {
            for ($i = 0; $i < mb_strlen($nGram); $i++) {
                if (mb_strlen($nGram) >= max(3, $minN)) {
                    $extraNGrams[] = mb_substr($nGram, 0, $i) . '_' . mb_substr($nGram, $i, mb_strlen($nGram) - $i);
                    if ($i > 0) {
                        $extraNGrams[] = mb_substr($nGram, 0, $i - 1) . '_' . mb_substr(
                                $nGram,
                                $i,
                                mb_strlen($nGram) - $i
                            );
                    }
                }
            }
        }
        return array_keys(array_flip($extraNGrams));
    }

    /**
     * Обновление поискового индекса для указанных сущностей
     *
     * Обновление происходит пачками по N сущностей (default=100)
     * для избежания превышения ограничений на использование памяти.
     *
     * По мере итерации для каждой сущности извлекаются n-граммы на указанные строковые поля.
     * Для полей из массива searchFields извлекаются n-граммы с учетом опечаток.
     * Для полей из массива searchSubstringFields извлекаются n-граммы без учета отпечаток (ticker, isin).
     *
     * Все n-граммы каждой пачки сущностей группируются в ассоциативный массив $groupByNGram,
     * в качестве ключа берется n-грамма, а в качестве значения массив вида [score1, instrumentId1, score2, instrumentId2, ...]
     *
     * Score учитывает длину n-граммы итоговый вес является умножением модификатора веса, дефолтного веса и длины n-граммы.
     *
     * Дефолтное значение score принимаем
     * 20 в случае в случае полного совпадения поисковой строки с n-граммой
     * 10 – для основной n-граммы (не учитывающей опечатки),
     * 1 – в остальных случаях.
     *
     * В рамках каждой группы в pipeline redis помещаем команды zAdd,
     * добавляющей id сущностей с весами в z-set с ключом соответстующем n-грамме.
     *
     * @param Generator $items
     * @param int $updateChunkSize Количество сущностей для обновления в одну итерацию
     * @param int $clearChunkSize Количество сущностей для очистки в одну итерацию
     * @return int Количество сделанных записей в БД
     */
    public function updateSearchIndex(Generator $items, int $updateChunkSize = 100, int $clearChunkSize = 1000): int
    {
        $chunkCount = 0;

        $count = 0;
        $searchKeyPrefix = $this->searchKeyPrefix . $this->keyPrefix;
        $searchFilterKeyPrefix = $this->searchFilterKeyPrefix . $this->keyPrefix;
        $searchIdField = $this->searchIdField ?? 'itemId';

        $groupByNGram = [];
        $groupByFilter = [];

        while (true) {
            if ($chunkCount >= $updateChunkSize || !$items->valid()) {
                $client = $this->connectionInstance->pipeline();
                foreach ($groupByNGram as $nGram => $values) {
                    $client = $client->zAdd($searchKeyPrefix . $nGram, ['NX'], ...$values);
                }
                foreach ($groupByFilter as $filter => $values) {
                    $client = $client->zAdd($searchFilterKeyPrefix . $filter, ['NX'], ...$values);
                }
                $count += array_sum($client->exec());
                $chunkCount = 0;
                $groupByNGram = [];
                $groupByFilter = [];
            }
            if (!$items->valid()) {
                break;
            }
            $item = $items->current();
            $itemId = $item[$searchIdField];

            foreach ($this->searchFilterFields ?? [] as $searchFilterField) {
                if (is_string($searchFilterField)) {
                    if (array_key_exists($searchFilterField, $item) && !is_null($item[$searchFilterField])) {
                        $value = is_string($item[$searchFilterField])
                            ? $item[$searchFilterField]
                            : json_encode($item[$searchFilterField]);
                        $groupByFilter[$searchFilterField . '__' . $value][] = 1;
                        $groupByFilter[$searchFilterField . '__' . $value][] = $itemId;
                    }
                } elseif (is_array($searchFilterField)) {
                    $key = $searchFilterField[0];
                    $value = $searchFilterField[1]($item);
                    $groupByFilter[$key . '__' . $value][] = 1;
                    $groupByFilter[$key . '__' . $value][] = $itemId;
                }
            }

            foreach ($this->params as $key => $param) {
                $valueParam = $param['value'] ?? null;
                $type = $param['type'] ?? 'full';
                $weightParam = $param['weight'] ?? 1;
                $minQueryLengthParam = $param['minQueryLength'] ?? 3;
                $aliases = $param['aliases'] ?? [];

                if (!is_null($valueParam) && is_callable($valueParam)) {
                    $value = $valueParam($item);
                } elseif (array_key_exists($key, $item)) {
                    $value = $item[$key];
                } else {
                    $value = null;
                }

                if (is_callable($weightParam)) {
                    $weight = $weightParam($item);
                } else {
                    $weight = $weightParam;
                }

                if (is_callable($minQueryLengthParam)) {
                    $minQueryLength = $minQueryLengthParam($item);
                } else {
                    $minQueryLength = $minQueryLengthParam;
                }

                if (is_null($value)) {
                    continue;
                }

                if ($type === 'exact') {
                    $fullString = mb_strtolower($value);
                    // Значительно увеличиваем приоритет выдачи точного совпадения над остальными
                    $groupByNGram[$fullString][] = 20 * $weight * mb_strlen($fullString);
                    $groupByNGram[$fullString][] = $itemId;
                } elseif ($type === 'substr' || $type === 'full') {
                    $nGrams = $this->getNGrams($value, $aliases, $minQueryLength);
                    foreach ($nGrams as $nGram) {
                        // Увеличиваем вес выдачи по подстроке на порядок по сравнению с выдачей учитывающей ошибки
                        // Это дает более релевантный список и при этом поиск по строке с опечаткой дает хороший результат
                        $groupByNGram[$nGram][] = 10 * $weight * mb_strlen($nGram);
                        $groupByNGram[$nGram][] = $itemId;
                    }
                    if ($type === 'full') {
                        $extraNGrams = $this->getExtraNGrams($value, $aliases, $minQueryLength);
                        foreach ($extraNGrams as $nGram) {
                            $groupByNGram[$nGram][] = $weight * mb_strlen($nGram);
                            $groupByNGram[$nGram][] = $itemId;
                        }
                    }
                }
            }

            $chunkCount++;
            $items->next();
        }

        $this->clearSearchIndexByPattern($this->searchResultKeyPrefix . $this->keyPrefix . '*', $clearChunkSize);

        return $count;
    }

    /**
     * Удаление всех записей, связанных с поисковым индексом
     *
     * @param int $clearChunkSize Количество сущностей для очистки в одну итерацию
     * @return int Количество удаленных ключей
     */
    public function clearSearchIndex(int $clearChunkSize = 1000): int
    {
        $count = $this->clearSearchIndexByPattern(
            $this->searchKeyPrefix . $this->keyPrefix . '*',
            $clearChunkSize
        );
        $count += $this->clearSearchIndexByPattern(
            $this->searchFilterKeyPrefix . $this->keyPrefix . '*',
            $clearChunkSize
        );
        $count += $this->clearSearchIndexByPattern(
            $this->searchResultKeyPrefix . $this->keyPrefix . '*',
            $clearChunkSize
        );
        return $count;
    }

    /**
     * Поиск сущностей по строке
     *
     * Как работает:
     *
     * Запрос разбивается на слова, для каждого слова извлекаются n-граммы (с опечатками в том числе),
     * По каждому слову массив n-грамм преобразуется в массив ключей вида search:instruments:ngram,
     * затем производится объединение zUnionStore z-массивов с суммированием весов и
     * записью результата по временному ключу с коротким временем жизни, данный ключ сохраняется в массиве $intersectingKeys.
     *
     * Все ключи, сохраненные в $intersectingKeys участвуют в операции пересечения z-массивов с суммированием весов,
     * результатом является z-массив, имеющий заданное время жизни (кеширование запроса),
     * который далее отдается как результат поиска с учетом направления сортировки,
     * смещения и лимита элементов, а так же минимальным суммарным весом элементов.
     *
     * Для получения более релевантных значений из результирущего ассоциативный массива
     * возвращаются элементы, вес которых составляет не менее 10% от максимального веса (первого элемента)
     *
     * @param string $query
     * @param array $filters
     * @param int $offset
     * @param int $limit
     * @return array
     */
    public function search(string $query, array $filters = [], int $offset = 0, int $limit = INF): array
    {
        $searchResultKeyPrefix = $this->searchResultKeyPrefix . $this->keyPrefix;
        $searchFilterKeyPrefix = $this->searchFilterKeyPrefix . $this->keyPrefix;
        $resultKey = $searchResultKeyPrefix . $query . (empty($filters) ? '' : ('__' . json_encode($filters)));
        if (!$this->exists($resultKey)) {
            $intersectingKeys = [];
            $client = $this->connectionInstance->pipeline();
            foreach ($filters as $field => $values) {
                $tmpFilterKey = $searchFilterKeyPrefix . 'tmp:' . $field . '__' . implode('__', $values);
                $intersectingKeys[] = $tmpFilterKey;
                $cb = function ($value) use ($field, $searchFilterKeyPrefix) {
                    return $searchFilterKeyPrefix . $field . '__'
                        . (is_string($value) ? $value : json_encode($value));
                };
                $client = $client->zUnionStore($tmpFilterKey, array_map($cb, $values));
                $client = $client->expire($tmpFilterKey, 10);
            }
            foreach ($this->getWords($query) as $word) {
                $tmpResultKey = $searchResultKeyPrefix . 'tmp:' . $word;
                $intersectingKeys[] = $tmpResultKey;
                $queryNGrams = array_merge($this->getNGrams($word), $this->getExtraNGrams($word));
                $queryNGrams[] = mb_strtolower($query);
                $cb = function ($queryNGram) {
                    return ($this->searchKeyPrefix . $this->keyPrefix) . $queryNGram;
                };
                $client = $client->zUnionStore($tmpResultKey, array_map($cb, $queryNGrams));
                $client = $client->expire($tmpResultKey, 10);
            }
            if (empty($intersectingKeys)) {
                return [];
            }
            $client = $client->zInterStore($resultKey, $intersectingKeys);
            $client = $client->expire($resultKey, $this->searchResultTtl);
            $client->exec();
        }
        $first = $this->zrevrangebyscore($resultKey, INF, $this->minResultScore, 0, 1, true);

        return $this->zrevrangebyscore(
            $resultKey,
            INF,
            empty($first) ? $this->minResultScore : array_values($first)[0] / 10,
            $offset,
            $limit
        );
    }
}
