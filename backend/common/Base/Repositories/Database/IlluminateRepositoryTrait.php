<?php

namespace Common\Base\Repositories\Database;

use Closure;
use Generator;
use ReflectionException;
use Throwable;
use LogicException;
use stdClass;

use Common\Base\MetaInformation\MetaInformation;
use Common\Base\MetaInformation\MetaInformationContainer;
use Common\Base\Utils\TransformationUtils;

use Illuminate\Database\Connection;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Facades\DB;

/**
 * Trait IlluminateRepositoryTrait
 * @package Common\Repositories\Traits
 *
 * @property string $connection Имя подключения
 * @property string $table Имя таблицы, с которой связана сущность
 * @property string $entity Класс сущности, с которой связан репозиторий
 *
 * @property string[] $dates Массив полей для работы с датами
 *   Для перечисленных в массиве полей будет выполнено преобразование
 *   int -> timestamp при добавлении записи и обратное преобразование
 *   при считывании данных. Обычно в этом массиве перечислены все поля
 *   с типом данных `timestamp`.
 *
 * @property string[] $fields Массив выбираемых полей
 *   Если свойство не определено, выбираются все поля (select * from ...).
 *   Если свойство определено, то выбираются только соответствующие поля.
 *   Репозиторий обрабатывает такие поля следующим образом:
 *
 *     1. Если ключ элемента массива является числом, то к выборке добавляется
 *        выражение `select field`, где field - значение элемента массива.
 *     2. Если ключ элемента массива является строкой, то к выборке добавляется
 *        выражение`select field as name`,
 *        где field - значение элемента массива, name - ключ элемента массива.
 *
 *        Пример:
 *
 *        ```
 *        protected $fields = [
 *            'status',
 *            'created_at' => 'time_created',
 *        ];
 *        ```
 *
 *        Приведет к формированию запроса вида:
 *
 *        ```
 *        select status, time_created as created_at from ...
 *        ```
 *
 * @property string[] $localFields Массив локальных полей
 *   Здесь перечислены поля сущности, не хранящиеся в базе данных.
 *   Репозиторий не добавляет такие поля к запросам
 *   на добавление или обновление записи.
 *   Имена полей должны быть в "snake_case" нотации.
 *
 * @property array $generatedFields Массив генерируемых полей
 *   Здесь перечислены имена полей, за обновление которых отвечает
 *   триггер базы данных. Репозиторий не добавляет такие поля к запросам
 *   на добавление или обновление записи.
 *
 * @property array $calculatedFields Массив вычисляемых полей
 *   Ассоциативный массив [`имя` => `значение`] вычисляемых полей.
 *   Репозиторий обрабатывает такие поля следующим образом:
 *
 *     1. Если `значение` массива строка, то в выражения с select добавляется `expression as field`.
 *        Это правило актуально для следующего формата:
 *
 *        ```
 *        protected $calculatedFields = [
 *            'status' => 5,
 *            'created_at' => 'time_created',
 *        ];
 *        ```
 *
 *     2. Если `значение` массива ассоциативный массив с полями (`binding`, `entity`, `table`),
 *        то в выражение с select добавляется подзапрос для выборки массива сущностей.
 *        Далее при декодировании репозиторий трансформирует выбранные подзапросом данные
 *        в массив сущностей и присваивает этот массив соответствующему полю корневой сущности.
 *
 *        На практике это обозначает, что имея классы `Example` и `Sample`:
 *
 *        ```
 *        class Sample {
 *          private $a;
 *        }
 *
 *        class Example {
 *          private Sample[] $samples;
 *        }
 *        ```
 *
 *        можно указать, из какой таблицы выбирать массив сущностей для
 *        поля `samples` следующим образом:
 *
 *        ```
 *        protected $entity = Example::class;
 *        protected $table = 'example_table';
 *
 *        protected $calculatedFields = [
 *            'samples' => [
 *              'binding' => [['example_id', 'id']],
 *              'entity' => Sample::class,
 *              'table' => 'samples_table',
 *            ],
 *        ];
 *        ```
 *
 *        В поле biding можно указать одну или несколько зависимостей для связывания
 *        записей, сначала указывается поле дочерней таблицы, затем соответствующее поле
 *        базовой таблицы. В примере выше мы связали таблицу `samples_table`
 *        с таблицей `example` по следующему условию `samples_table.example_id = example_table.id`.
 *
 *        Дополнительно можно задать поля `dates` для `Sample::class`.
 *        Дополнительно можно задать поле `fields` со списком выбираемых полей (string, поля указывать через запятую)
 *        Дополнительно можно задать поле `filters` со списком фильтров
 *        Если не задать поле `entity`, то вернется массив значений поля, заданного в `array_field`
 *
 *        Если задать признак 'array' => false, то в сеттер попадет одна сущность вместо массива.
 *        При этом если в результате join будет выбрано больше одной записи, будет выброшено исключение.
 */
trait IlluminateRepositoryTrait
{
    /**
     * @var Connection
     */
    protected $connectionInstance;

    /**
     * IlluminateRepositoryTrait constructor.
     */
    public function __construct()
    {
        if (empty($this->connection)) {
            throw new LogicException('missing connection for ' . self::class);
        }
        if (empty($this->entity)) {
            throw new LogicException('missing entity for ' . self::class);
        }
        if (empty($this->table)) {
            throw new LogicException('missing table for ' . self::class);
        }

        $this->connectionInstance = DB::connection($this->connection);
    }

    public function refreshConnection()
    {
        $this->connectionInstance = DB::connection($this->connection);
    }

    /**
     * @return Connection
     */
    protected function getConnection(): Connection
    {
        return $this->connectionInstance;
    }

    /**
     * @return string
     */
    protected function getConnectionName(): string
    {
        return $this->connection;
    }

    /**
     * Получить метаинформацию для класса
     *
     * @param object $class
     *
     * @return MetaInformation
     * @throws ReflectionException
     */
    protected function getMetaInformation(object $class): MetaInformation
    {
        return MetaInformationContainer::get(get_class($class));
    }

    /**
     * @param int $entityId
     * @return mixed|null
     */
    protected function getEntityById(int $entityId)
    {
        $collection = $this->getCollection([["{$this->table}.id", $entityId]]);

        if (empty($collection)) {
            return null;
        }

        if (count($collection) > 1) {
            throw new LogicException('missing primary key for ' . self::class);
        }

        return $collection[0];
    }

    /**
     * @param array $filters
     * @return mixed|null
     */
    protected function getEntityByKey(array $filters = [])
    {
        $collection = $this->getCollection($filters);

        if (empty($collection)) {
            return null;
        }

        if (count($collection) > 1) {
            throw new LogicException('missing key for ' . self::class);
        }

        return $collection[0];
    }

    /**
     * @param int $entityId
     * @return bool
     */
    protected function entityExists(int $entityId): bool
    {
        $builder = $this->getConnection()->table($this->table);
        $this->applyFilters($builder, [["{$this->table}.id", $entityId]]);
        return $builder->exists();
    }

    /**
     * @param array $filters
     * @param float $limit the default value is infinity
     * @param int $offset
     * @param array $orderBy
     * @param array $fields
     * @return Generator
     * @throws Throwable
     */
    protected function getCollectionIterator(
        array $filters = [],
        float $limit = INF,
        int $offset = 0,
        array $orderBy = [],
        array $fields = ['*']
    ): Generator {
        $collection = $this->getCollectionQuery($filters, $limit, $offset, $orderBy, $fields)->get();

        foreach ($collection as $raw) {
            yield $this->decodeRaw($raw);
        }
    }

    /**
     * @param array $filters
     * @param float $limit the default value is infinity
     * @param int $offset
     * @param array $orderBy
     * @param array $fields
     * @return array
     */
    protected function getCollection(
        array $filters = [],
        float $limit = INF,
        int $offset = 0,
        array $orderBy = [],
        array $fields = ['*']
    ): array {
        return iterator_to_array($this->getCollectionIterator($filters, $limit, $offset, $orderBy, $fields));
    }

    /**
     * @param string $key
     * @param array $filters
     * @param float $limit
     * @param int $offset
     * @param array $orderBy
     * @param array|string[] $fields
     * @return Generator
     */
    protected function getCollectionMapIterator(
        string $key,
        array $filters = [],
        float $limit = INF,
        int $offset = 0,
        array $orderBy = [],
        array $fields = ['*']
    ): Generator {
        $getter = 'get' . ucfirst($key);
        foreach ($this->getCollectionIterator($filters, $limit, $offset, $orderBy, $fields) as $entity) {
            if (!method_exists($entity, $getter)) {
                throw new LogicException("getter method does not exist for the entity " . get_class($entity));
            }

            yield $entity->$getter() => $entity;
        }
    }

    /**
     * @param string $key
     * @param array $filters
     * @param float $limit
     * @param int $offset
     * @param array $orderBy
     * @param array|string[] $fields
     * @return array
     */
    protected function getCollectionMap(
        string $key,
        array $filters = [],
        float $limit = INF,
        int $offset = 0,
        array $orderBy = [],
        array $fields = ['*']
    ): array {
        $map = [];
        foreach ($this->getCollectionMapIterator($key, $filters, $limit, $offset, $orderBy, $fields) as $k => $entity) {
            if (array_key_exists($key, $map)) {
                throw new LogicException("duplicate key {$key} for collection map of " . get_class($entity));
            }
            $map[$k] = $entity;
        }

        return $map;
    }

    /**
     * @param string $key
     * @param array $filters
     * @param float $limit
     * @param int $offset
     * @param array $orderBy
     * @param array|string[] $fields
     * @return array
     */
    protected function getCollectionGroupBy(
        string $key,
        array $filters = [],
        float $limit = INF,
        int $offset = 0,
        array $orderBy = [],
        array $fields = ['*']
    ): array {
        $map = [];
        foreach ($this->getCollectionMapIterator($key, $filters, $limit, $offset, $orderBy, $fields) as $k => $entity) {
            if (array_key_exists($key, $map)) {
                throw new LogicException("duplicate key {$key} for collection map of " . get_class($entity));
            }
            $map[$k][] = $entity;
        }

        return $map;
    }

    /**
     * @param array $filters
     * @return int
     */
    protected function getCollectionLength(array $filters = []): int
    {
        return $this->getCollectionQuery($filters)->count();
    }

    /**
     * @param array $filters
     * @param float $limit the default value is infinity
     * @param int $offset
     * @param array $orderBy
     * @param array $fields
     * @return Builder
     */
    protected function getCollectionQuery(
        array $filters = [],
        float $limit = INF,
        int $offset = 0,
        array $orderBy = [],
        array $fields = ['*']
    ): Builder {
        $builder = $this->getConnection()->table($this->table);
        $allFields = in_array('*', $fields);
        $fields = $allFields
            ? ($this->fields ?? ['*'])
            : $fields;

        $calculatedFields = [];
        if (!empty($this->calculatedFields ?? [])) {
            $fields = array_filter(
                $fields,
                function ($field) use (&$calculatedFields) {
                    if (!array_key_exists($field, $this->calculatedFields ?? [])) {
                        return true;
                    }

                    $calculatedFields[] = $field;
                    return false;
                }
            );
        }

        if (reset($fields) === '*') {
            $builder->addSelect(['*']);
        } else {
            foreach ($fields as $key => $value) {
                if (is_string($key)) {
                    $builder->addSelect("{$value} as {$key}");
                } else {
                    $builder->addSelect($value);
                }
            }
        }

        $this->applyFilters($builder, $filters);

        if (!is_infinite($limit)) {
            $builder->limit($limit);
        }

        $builder->offset($offset);
        foreach ($orderBy as $rule) {
            $builder->orderBy($rule[0], count($rule) > 1 ? $rule[1] : 'asc');
        }

        foreach ($this->calculatedFields ?? [] as $field => $expression) {
            if ($allFields || in_array($field, $calculatedFields)) {
                $builder->addSelect($this->getCalculatedFieldExpression($field, $expression));
            }
        }

        return $builder;
    }

    /**
     * @param string $field
     * @param string|array $expression
     * @return Expression
     */
    protected function getCalculatedFieldExpression(string $field, $expression)
    {
        if (!is_array($expression)) {
            return new Expression("$expression as $field");
        }

        $binding = implode(
            ' and ',
            array_map(
                function ($item) {
                    return "{$item[0]} = {$this->table}.{$item[1]}";
                },
                $expression['binding']
            )
        );

        $fields = $expression['fields'] ?? '*';

        $filterExpression = implode(
            ' and',
            array_map(fn($filter) => $this->getFilterExpression($filter), $expression['filters'] ?? [])
        );
        if (!empty($filterExpression)) {
            $filterExpression = 'and ' . $filterExpression;
        }

        return new Expression(
            "(select array_to_json(array_agg({$field})) from (select {$fields} from {$expression['table']}" .
            " where {$binding} {$filterExpression}) as {$field}) as {$field}"
        );
    }

    protected function getFilterExpression(array $filter): string
    {
        switch (count($filter)) {
            case 0:
                return '';
            case 2:
                return "{$filter[0]} = '{$filter[1]}'";
            case 3:
                if (strtolower($filter[1]) === 'in') {
                    $keys = implode(', ', array_map(fn($key) => "'{$key}'", $filter[2]));
                    return "{$filter[0]} in ({$keys})";
                }
                break;
        }
        throw new LogicException('unsupported filter');
    }

    /**
     * @param stdClass $raw
     * @param string|null $entity
     * @return mixed
     */
    protected function decodeRaw(stdClass $raw, ?string $entity = null)
    {
        foreach ($this->dates ?? [] as $time) {
            if (property_exists($raw, $time)) {
                $raw->$time = TransformationUtils::stringToTimestamp($raw->$time);
            }
        }

        $entity = !empty($entity) ? new $entity() : new $this->entity();
        if (!method_exists($entity, 'fromObject')) {
            throw new LogicException('missing fromObject for ' . $this->entity);
        }

        $this->decodeRawCalculatedFields($raw);

        return $entity::fromObject($raw, true, true);
    }

    /**
     * @param stdClass $raw
     */
    protected function decodeRawCalculatedFields(stdClass $raw)
    {
        foreach ($this->calculatedFields ?? [] as $field => $expression) {
            if (!is_array($expression)) {
                continue;
            }

            if (property_exists($raw, $field)) {
                $class = $expression['entity'] ?? null;
                $arrayField = $expression['array_field'] ?? null;

                if (is_null($class) && is_null($arrayField)) {
                    throw new LogicException('incorrect settings for fields entity and array_field');
                }

                $raw->$field = array_map(
                    function ($item) use ($class, $arrayField, $expression) {
                        foreach ($expression['dates'] ?? [] as $time) {
                            if (property_exists($item, $time)) {
                                $item->$time = TransformationUtils::stringToTimestamp($item->$time);
                            }
                        }
                        if (!is_null($arrayField) && !isset($item->{$arrayField})) {
                            throw new LogicException('incorrect settings for field array_field');
                        }
                        return $class
                            ? $class::fromObject($item, true, true)
                            : $item->{$arrayField};
                    },
                    json_decode($raw->$field) ?? []
                );

                if (($expression['array'] ?? true) === false) {
                    if (count($raw->$field) > 1) {
                        throw new LogicException("incorrect settings for field \"{$field}\"");
                    }

                    $raw->$field = $raw->$field[0] ?? null;
                }
            }
        }
    }

    /**
     * Encode fields for insert to database
     *
     * @param object $entity
     * @param array $fields
     *
     * @return array
     * @throws ReflectionException
     */
    protected function encodeFields(object $entity, array $fields = []): array
    {
        if (!method_exists($entity, 'toArray')) {
            throw new LogicException('missing method toArray for ' . get_class($entity));
        }

        if (!empty($this->localFields) && empty($fields)) {
            $fields = array_diff($this->getMetaInformation($entity)->getClassPropertiesNames(), $this->localFields);
        }

        $values = $entity->toArray($fields, true, true);

        foreach ($this->localFields ?? [] as $localField) {
            unset($values[$localField]);
        }

        foreach ($this->dates ?? [] as $field) {
            if (array_key_exists($field, $values) && !is_null($values[$field])) {
                $values[$field] = TransformationUtils::timestampToDateTime($values[$field], true);
            }
        }

        $encodedValues = $values;
        foreach ($this->fields ?? [] as $key => $name) {
            $nameWithTable = explode('.', $name);
            if (count($nameWithTable) > 1) {
                if ($nameWithTable[0] !== $this->table) {
                    unset($encodedValues[$key]);
                    continue;
                }
                $name = $nameWithTable[1];
            }
            if (array_key_exists($key, $values)) {
                unset($encodedValues[$key]);
                $encodedValues[$name] = $values[$key];
            }
        }

        return $encodedValues;
    }

    /**
     * @param Builder $builder
     * @param array $filters
     */
    protected function applyFilters(Builder $builder, array $filters)
    {
        if (!empty($filters)) {
            foreach ($filters as $filter) {
                $this->applyFilter($builder, $filter);
            }
        }
    }

    /**
     * @param Builder $builder
     * @param array $filter
     */
    protected function applyFilter(Builder $builder, array $filter)
    {
        if (empty($filter)) {
            return;
        }

        if ($filter[0] instanceof Closure) {
            $builder->where($filter[0]);
            return;
        }

        switch (count($filter)) {
            case 2:
                $builder->where($filter[0], $filter[1]);
                break;
            case 3:
                if (strtolower($filter[1]) === 'in') {
                    $builder->whereIn($filter[0], $filter[2]);
                    break;
                } elseif (strtolower($filter[1]) === 'jsoncontains') {
                    $builder->whereJsonContains($filter[0], $filter[2]);
                    break;
                }
                $builder->where($filter[0], $filter[1], $filter[2]);
                break;
            case 4:
                $builder->where($filter[0], $filter[1], $filter[2], $filter[3]);
                break;
            default:
                throw new LogicException('unsupported filter');
        }
    }

    /**
     * @param object $entity
     * @param $fields
     * @throws Throwable
     */
    protected function addEntityProcessFields(object $entity, &$fields)
    {
        $fields = $this->encodeFields($entity);
        if (array_key_exists('id', $fields) && empty($fields['id'])) {
            unset($fields['id']);
        }

        foreach ($this->localFields ?? [] as $field) {
            if (array_key_exists($field, $fields)) {
                unset($fields[$field]);
            }
        }

        foreach ($this->calculatedFields ?? [] as $field => $expression) {
            if (array_key_exists($field, $fields)) {
                unset($fields[$field]);
            }
        }

        foreach ($this->generatedFields ?? [] as $field) {
            if (array_key_exists($field, $fields)) {
                unset($fields[$field]);
            }
        }
    }

    /**
     * Применяет изменившиеся значения к редактируемой сущности
     *
     * @param object $entity
     * @param object $newEntity
     *
     * @throws ReflectionException
     */
    protected function applyNewValues(object $entity, object $newEntity): void
    {
        if (!empty($this->localFields)) {
            $fields = array_diff($this->getMetaInformation($newEntity)->getClassPropertiesNames(), $this->localFields);
        }

        $newValues = $newEntity->toArray($fields ?? []);
        foreach ($this->localFields ?? [] as $field) {
            if (array_key_exists($field, $newValues)) {
                unset($newValues[$field]);
            }
        }

        $entity->applyPropertiesArray($newValues);
    }

    /**
     * @param object $entity
     */
    protected function addEntity(object $entity)
    {
        if (!method_exists($entity, 'setId')) {
            throw new LogicException('missing method setId for ' . get_class($entity));
        }

        $this->addEntityProcessFields($entity, $fields);

        $entityId = $this->getConnection()->table($this->table)->insertGetId($fields);

        if (empty($entityId)) {
            throw new LogicException('missing entity id');
        }
        $entity->setId($entityId);
    }

    /**
     * @param object $entity
     *
     * @throws Throwable
     */
    protected function addEntityWithApplyResult(object $entity)
    {
        $this->addEntityProcessFieldsWithApplyResult($entity);
    }

    /**
     * Добавить запись, если не существует по указанным полям индекса
     *
     * @param mixed $entity Сущность, которую надо добавить, если такой еще нет по конкретным полям
     * @param array $indexFields Список полей, по которым надо проверить наличие записи в БД.
     *      На этих полях должен быть настроен индекс или первичный ключ
     *
     * @throws Throwable
     */
    protected function addEntityIfNotExist(object $entity, array $indexFields): void
    {
        $this->addEntityProcessFieldsWithApplyResult($entity, $indexFields);
    }

    /**
     * @param object $entity
     * @param array $fields
     * @param array $filters
     */
    protected function updateEntityProcessFields(object $entity, array &$fields, array &$filters)
    {
        $fields = $this->encodeFields($entity, $fields);

        $filters = $this->getEntityKey($entity);
        if (empty($filters)) {
            throw new LogicException('missing key for ' . get_class($entity));
        }

        foreach ($this->localFields ?? [] as $field) {
            if (array_key_exists($field, $fields)) {
                unset($fields[$field]);
            }
        }

        foreach ($this->calculatedFields ?? [] as $field => $expression) {
            if (array_key_exists($field, $fields)) {
                unset($fields[$field]);
            }
        }

        foreach ($filters as $field) {
            if (count($field) !== 2) {
                throw new LogicException('incorrect key structure for ' . get_class($entity));
            }

            [$key, $value] = $field;
            if (empty($key) || empty($value)) {
                throw new LogicException("incorrect unit [{$key}-{$value}] for " . get_class($entity));
            }

            unset($fields[$key]);
        }

        foreach ($this->generatedFields ?? [] as $field) {
            if (array_key_exists($field, $fields)) {
                unset($fields[$field]);
            }
        }
    }

    /**
     * @param object $entity
     * @param array $fields
     */
    protected function updateEntity(object $entity, $fields = [])
    {
        $filters = [];
        $this->updateEntityProcessFields($entity, $fields, $filters);

        $builder = $this->getConnection()->table($this->table);
        $this->applyFilters($builder, $filters);

        $builder->update($fields);
    }

    /**
     * @param object $entity
     * @param array $fields
     */
    protected function updateEntityWithApplyResult(object $entity, $fields = [])
    {
        $filters = [];
        $this->updateEntityProcessFields($entity, $fields, $filters);

        $key = $this->getEntityKey($entity);
        $builder = $this->getConnection()->table($this->table)->where($key);
        $sql = $builder->grammar->compileUpdate($builder, $fields);

        $returning = '*';
        foreach ($this->calculatedFields ?? [] as $field => $expression) {
            $returning .= ", {$this->getCalculatedFieldExpression($field, $expression)}";
        }

        $raw = $this->getConnection()
            ->select(
                "$sql returning $returning",
                array_merge(
                    array_values($fields),
                    array_map(
                        function ($item) {
                            return $item[1];
                        },
                        $key
                    )
                )
            );
        $updatedEntity = $this->decodeRaw($raw[0]);

        $this->applyNewValues($entity, $updatedEntity);
    }

    /**
     * @param object $entity
     * @return array
     */
    protected function getEntityKey(object $entity)
    {
        if (!method_exists($entity, 'getId')) {
            throw new LogicException('missing method getId for ' . get_class($entity));
        }

        return [
            ['id', $entity->getId()]
        ];
    }

    /**
     * @param int $id
     */
    protected function deleteEntity(int $id)
    {
        $this->getConnection()->table($this->table)->delete($id);
    }

    /**
     * Удалить сущности по указанным фильтрам
     *
     * @param array $filters
     */
    protected function deleteEntities(array $filters): void
    {
        $builder = $this->getConnection()->table($this->table);
        $this->applyFilters($builder, $filters);
        $builder->delete();
    }

    /**
     * @param array $filters
     * @param array $values
     * @throws LogicException
     */
    protected function updateEntities(array $filters, array $values)
    {
        if (empty($values)) {
            throw new LogicException('missing values for update');
        }

        $builder = $this->getConnection()->table($this->table);
        $this->applyFilters($builder, $filters);

        $builder->update($values);
    }

    /**
     * @param array $entities
     * @throws Throwable
     */
    protected function addEntities(array $entities)
    {
        $rows = [];
        foreach ($entities as $entity) {
            $this->addEntityProcessFields($entity, $fields);
            $rows[] = $fields;
        }

        $this->getConnection()->table($this->table)->insert($rows);
    }

    /**
     * Процесс добавления сущности с применением результата на эту сущность
     *
     * @param object $entity
     * @param array $onConflictFields Список полей для определения первичного ключа или индекса.
     *      Если запись по данным полям уже существует, то она не будет добавлена. Не подставляется в запрос, если массив пустой.
     *
     * @throws Throwable
     */
    protected function addEntityProcessFieldsWithApplyResult(object $entity, array $onConflictFields = []): void
    {
        $this->addEntityProcessFields($entity, $fields);
        $builder = $this->getConnection()->table($this->table);
        $sql = $builder->grammar->compileInsert($builder, $fields);

        $returning = '*';
        foreach ($this->calculatedFields ?? [] as $field => $expression) {
            $returning .= ", {$this->getCalculatedFieldExpression($field, $expression)}";
        }

        $onConflict = '';
        if (!empty($onConflictFields)) {
            $indexFieldsRaw = implode(', ', $onConflictFields);
            $onConflict = "on conflict ({$indexFieldsRaw}) do nothing";
        }

        $sql = "{$sql} {$onConflict} returning {$returning}";

        $raw = $this->getConnection()->select($sql, array_values($fields));
        if (!empty($onConflictFields) && empty($raw[0])) {
            return;
        }

        $newEntity = $this->decodeRaw($raw[0]);
        $this->applyNewValues($entity, $newEntity);
    }

    /**
     * @param Closure $func
     * @throws Throwable
     */
    public function executeTransaction(Closure $func): void
    {
        try {
            $this->beginTransaction();
            $func();
            $this->commitTransaction();
        } catch (Throwable $throwable) {
            $this->rollbackTransaction();
            throw $throwable;
        }
    }

    /**
     * Begin transaction
     * @throws Throwable
     */
    public function beginTransaction()
    {
        $this->getConnection()->beginTransaction();
    }

    /**
     * Rollback transaction
     * @throws Throwable
     */
    public function rollbackTransaction()
    {
        $this->getConnection()->rollBack();
    }

    /**
     * Commit transaction
     * @throws Throwable
     */
    public function commitTransaction()
    {
        $this->getConnection()->commit();
    }
}
