<?php

namespace Common\Base\Entities;

use ReflectionException;
use stdClass;

use Common\Base\MetaInformation\MetaInformation;
use Common\Base\MetaInformation\MetaInformationContainer;
use Common\Base\Utils\TransformationUtils;

/**
 * Trait SerializableTrait
 * @package Common\Entities\Traits
 */
trait SerializableTrait
{
    /**
     * @param array $array
     * @return static
     * @throws ReflectionException
     */
    public static function fromArray(array $array): self
    {
        $raw = TransformationUtils::arrayToObject($array);
        return static::fromObject($raw);
    }

    /**
     * @param $object
     * @return static
     * @throws ReflectionException
     */
    public static function fromObject($object): self
    {
        $obj = new static();
        return $obj->applyPropertiesObject($object);
    }

    /**
     * @param string $json
     * @return static
     * @throws ReflectionException
     */
    public static function fromJson(string $json): self
    {
        $raw = json_decode($json);
        return static::fromObject($raw);
    }

    /**
     * @param stdClass $object
     * @return static
     * @throws ReflectionException
     */
    public function applyPropertiesObject(stdClass $object): self
    {
        $metaInformation = $this->getMetaInformation($this);

        foreach ($object as $propertyName => $value) {
            if (($setter = $metaInformation->getSetter($propertyName))) {
                $this->$setter($value, $propertyName);
            } elseif ($metaInformation->getPropertyByName($propertyName)) {
                $this->$propertyName = $value;
            }
        }
        return $this;
    }

    /**
     * @param array $array
     * @return static
     * @throws ReflectionException
     */
    public function applyPropertiesArray(array $array)
    {
        $object = TransformationUtils::arrayToObject($array);
        return $this->applyPropertiesObject($object);
    }

    /**
     * @param array $fields
     * @param bool $withNullFields
     * @param bool $convertToUnderScore
     * @return false|string
     * @throws ReflectionException
     */
    public function toJson($fields = [], $withNullFields = true, $convertToUnderScore = true): string
    {
        $metaInformation = $this->getMetaInformation($this);
        $properties = $metaInformation->getClassPropertiesNames();

        $obj = new stdClass();
        foreach ($properties as $propertyName) {
            $formattedName = $convertToUnderScore
                ? TransformationUtils::stringCamelCaseToUnderScore($propertyName)
                : $propertyName;

            if (!empty($fields) && !in_array($propertyName, $fields)) {
                continue;
            }

            if (!is_null($this->$propertyName) || $withNullFields) {
                if (is_array($this->$propertyName)) {
                    $value = [];
                    foreach ($this->$propertyName as $key => $item) {
                        if (is_object($item) && method_exists($item, 'toJson')) {
                            $value[$key] = json_decode(
                                $item->toJson(
                                    [],
                                    $withNullFields,
                                    $convertToUnderScore
                                )
                            );
                        } else {
                            $value[$key] = $item;
                        }
                    }
                    $obj->$formattedName = $value;
                } elseif (is_object($this->$propertyName)) {
                    if (method_exists($this->$propertyName, 'toJson')) {
                        $obj->$formattedName = json_decode(
                            $this->$propertyName->toJson(
                                [],
                                $withNullFields,
                                $convertToUnderScore
                            )
                        );
                    } else {
                        $obj->$formattedName = $this->$propertyName;
                    }
                } else {
                    $obj->$formattedName = $this->$propertyName;
                }
            }
        }

        return json_encode($obj, JSON_UNESCAPED_UNICODE);
    }

    /**
     * @param array $fields
     * @param bool $withNullFields
     * @param bool $convertToUnderScore
     * @return stdClass
     * @throws ReflectionException
     */
    public function toObject($fields = [], $withNullFields = true, $convertToUnderScore = true): stdClass
    {
        return json_decode(json_encode($this->toArray($fields, $withNullFields, $convertToUnderScore)));
    }

    /**
     * @param array $fields
     * @param bool $withNullFields
     * @param bool $convertToUnderScore
     * @param callable|null $fieldsConverter
     * @return array
     * @throws ReflectionException
     */
    public function toArray(
        $fields = [],
        $withNullFields = true,
        $convertToUnderScore = true,
        callable $fieldsConverter = null
    ): array {
        return $this->objectToArray($this, $fields, $withNullFields, $convertToUnderScore, $fieldsConverter);
    }

    /**
     * @param object $object
     * @param string[] $fields
     * @param bool $withNullFields
     * @param bool $convertToUnderScore
     * @param callable|null $fieldsConverter
     * @return array
     * @throws ReflectionException
     */
    protected function objectToArray(
        object $object,
        array $fields = [],
        bool $withNullFields = true,
        bool $convertToUnderScore = true,
        callable $fieldsConverter = null
    ): array {
        if ($object instanceof stdClass) {
            return (array)$object;
        }
        $metaInformation = $this->getMetaInformation($object);
        $properties = $metaInformation->getProperties();

        $arr = [];
        foreach ($properties as $property) {
            $formattedName = $convertToUnderScore
                ? TransformationUtils::stringCamelCaseToUnderScore($property->getName())
                : $property->getName();

            if (!empty($fields) && !in_array($property->getName(), $fields)) {
                continue;
            }

            if ($withNullFields || !is_null($property->getValue($object))) {
                $value = $property->isInitialized($object) ? $property->getValue($object) : null;
                if (is_array($value)) {
                    $arr[$formattedName] = [];
                    foreach ($value as $item) {
                        $arr[$formattedName][] = $this->fieldsConverter(
                            $item,
                            $withNullFields,
                            $convertToUnderScore,
                            $fieldsConverter
                        );
                    }
                } else {
                    $arr[$formattedName] = $this->fieldsConverter(
                        $value,
                        $withNullFields,
                        $convertToUnderScore,
                        $fieldsConverter
                    );
                }
            }
        }

        return $arr;
    }

    /**
     * @param mixed $value
     * @param bool $withNullFields
     * @param bool $convertToUnderScore
     * @param callable|null $fieldsConverter
     * @return mixed
     * @throws ReflectionException
     */
    protected function fieldsConverter(
        $value,
        bool $withNullFields = true,
        bool $convertToUnderScore = true,
        callable $fieldsConverter = null
    ) {
        if ($fieldsConverter !== null) {
            return $fieldsConverter($value, $withNullFields, $convertToUnderScore);
        }

        if (!is_object($value)) {
            return $value;
        }

        $itemMetaInformation = $this->getMetaInformation($value);
        if ($itemMetaInformation->classHasToArrayMethod()) {
            return $value->toArray(
                [],
                $withNullFields,
                $convertToUnderScore,
                $fieldsConverter
            );
        }

        return $this->objectToArray($value, [], $withNullFields, $convertToUnderScore, $fieldsConverter);
    }

    /**
     * Получить метаинформацию для класса
     *
     * @param object $class
     *
     * @return \Common\Base\MetaInformation\MetaInformation
     * @throws ReflectionException
     */
    protected function getMetaInformation(object $class): MetaInformation
    {
        return MetaInformationContainer::get(get_class($class));
    }
}
