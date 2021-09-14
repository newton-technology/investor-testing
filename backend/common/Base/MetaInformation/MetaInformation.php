<?php

declare(strict_types=1);

namespace Common\Base\MetaInformation;

use LogicException;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;

use Common\Base\Utils\TransformationUtils;

/**
 * Класс, описывающий метаинформацию по заданной классу
 */
class MetaInformation
{
    /**
     * Имя класса сущности
     *
     * @var string
     */
    private string $className;

    /**
     * Рефлексия класса
     *
     * @var ReflectionClass
     */
    private ReflectionClass $reflectionClass;

    /**
     * Имя заявленное как наименование XML элемента
     *
     * @var string|null
     */
    private ?string $xmlName = null;

    /**
     * Ассоциативный массив проверенных на существование методов класса
     *
     * @var bool[]
     */
    private array $classMethodExists = [];

    /**
     * Массив для кеша не статичных приватных и защищенных свойств класса
     *
     * Ключом выступает исходное имя свойства
     *
     * @var array<string, ReflectionProperty>
     */
    private array $classProperties = [];

    /**
     * Массив для кеша не статичных приватных и защищенных свойств класса в underscore
     *
     * Ключом выступает исходное имя свойства
     *
     * @var array<string, ReflectionProperty>
     */
    private array $classPropertiesUnderscore = [];

    /**
     * Массив для кеша имен не статичных приватных и защищенных полей класса
     *
     * @var string[]
     */
    private array $classPropertiesNames = [];

    /**
     * Массив для кеша из имени поля в имя сеттера
     *
     * @var array<string, string|null>
     */
    private array $setterByPropertyName = [];

    /**
     * Массив для кеша из underscore имени поля в имя сеттера
     *
     * @var array<string, string|null>
     */
    private array $setterByPropertyNameUnderscore = [];

    /**
     * Поля класса, которые необходимо исключить из вывода в ответ
     *
     * @var string[]|null
     */
    private ?array $excludedProperties = null;

    /**
     * Ассоциативный массив, который кеширует соответствие полей и их наименований в качестве XML элементов
     *
     * @var array
     */
    private array $propertyXmlNameMap = [];

    /**
     * Ассоциативный массив, который кеширует соответствие полей и их наименований в качестве атрибутов XML элементов
     *
     * @var array
     */
    private array $propertyXmlAttributeNameMap = [];

    /**
     * Список полей класса, которые нужно отдать в ответе
     *
     * @var string[]|null
     */
    private ?array $responseableProperties = null;

    /**
     * MetaInformation constructor.
     *
     * @param string $className
     *
     * @throws ReflectionException
     */
    public function __construct(string $className)
    {
        $this->className = $className;
        $this->reflectionClass = new ReflectionClass($className);

        $this->parseProperties($this->reflectionClass);
    }

    private function parseProperties(ReflectionClass $reflectionClass): void
    {
        $properties = $reflectionClass->getProperties(
            ReflectionProperty::IS_PRIVATE | ReflectionProperty::IS_PROTECTED
        );
        foreach ($properties as $property) {
            if ($property->isStatic()) {
                continue;
            }
            $property->setAccessible(true);

            $propertyName = $property->getName();
            $underScorePropertyName = TransformationUtils::stringCamelCaseToUnderScore($propertyName);

            if (
                $propertyName !== $underScorePropertyName
                && property_exists($reflectionClass, $underScorePropertyName)
            ) {
                throw new LogicException(
                    "Entity {$this->className} has both underscore and camelcase field {$propertyName}"
                );
            }

            $this->classProperties[$propertyName] = $property;
            $this->classPropertiesUnderscore[$underScorePropertyName] = $property;
            $this->classPropertiesNames[$propertyName] = $propertyName;

            $setterName = 'set' . ucfirst($propertyName);
            $this->setterByPropertyName[$propertyName] = method_exists($this->className, $setterName)
                ? $setterName
                : null;
            $this->setterByPropertyNameUnderscore[$underScorePropertyName] = $this->setterByPropertyName[$propertyName];

            $docComment = !empty($property->getDocComment()) ? $property->getDocComment() : '';

            if (preg_match('/\*\s*@xml\s+([\w_-]+)\s*\n/', $docComment, $matches) === 1) {
                $this->propertyXmlNameMap[$property->getName()] = $matches[1];
            } else {
                $this->propertyXmlNameMap[$property->getName()] = $property->getName();
            }

            if (preg_match('/\*\s*@xmlAttribute\s+([\w_-]+)\s*\n/', $docComment, $matches) === 1) {
                $this->propertyXmlAttributeNameMap[$property->getName()] = $matches[1];
            }
        }
    }

    /**
     * Метод для получения сеттера по имени поля
     *
     * @param string $propertyName
     *
     * @return string|null
     */
    public function getSetter(string $propertyName): ?string
    {
        return $this->setterByPropertyName[$propertyName] ?? $this->setterByPropertyNameUnderscore[$propertyName] ?? null;
    }

    /**
     * Получить имена свойств класса
     *
     * @return string[]
     */
    public function getClassPropertiesNames(): array
    {
        return $this->classPropertiesNames;
    }

    /**
     * Проверить, имеет ли класс метод toArray
     *
     * @return bool
     */
    public function classHasToArrayMethod(): bool
    {
        return $this->classMethodExists('toArray');
    }

    /**
     * Проверить, имеет ли класс метод toResponse
     *
     * @return bool
     */
    public function classHasToResponseMethod(): bool
    {
        return $this->classMethodExists('toResponse');
    }

    /**
     * Проверить, имеет ли класс метод
     *
     * @param string $methodName
     * @return bool
     */
    public function classMethodExists(string $methodName): bool
    {
        if (!array_key_exists($methodName, $this->classMethodExists)) {
            $this->classMethodExists[$methodName] = method_exists($this->className, $methodName);
        }

        return $this->classMethodExists[$methodName];
    }

    /**
     * Получить объект класса рефлексии для указанного свойства
     *
     * @param string $propertyName
     *
     * @return ReflectionProperty|null
     */
    public function getPropertyByName(string $propertyName): ?ReflectionProperty
    {
        return $this->classProperties[$propertyName] ?? $this->classPropertiesUnderscore[$propertyName] ?? null;
    }

    /**
     * Получить список полей, которые необходимы для ответа запроса
     *
     * @return string[]
     */
    public function getResponseableFields(): array
    {
        if ($this->responseableProperties !== null) {
            return $this->responseableProperties;
        }

        return $this->responseableProperties = array_diff($this->getClassPropertiesNames(), $this->getExcludedFields());
    }

    /**
     * Получить поля, которые необходимо исключить из ответа
     *
     * @return string[]
     */
    public function getExcludedFields(): array
    {
        if ($this->excludedProperties !== null) {
            return $this->excludedProperties;
        }

        $properties = $this->getProperties();
        $excluded = [];
        foreach ($properties as $property) {
            if (preg_match('/\*\s*@excludeFromResponse.*\n/', $property->getDocComment() ?: '') === 1) {
                $excluded[] = $property->getName();
            }
        }

        return $this->excludedProperties = $excluded;
    }

    /**
     * Получить наименование XML элемента, соответствующего данного класса
     * @return string
     */
    public function getXMLName(): string
    {
        if ($this->xmlName !== null) {
            return $this->xmlName;
        }

        if (preg_match('/\*\s*@xml\s+([\w_-]+)\s*\n/', $this->reflectionClass->getDocComment() ?? '', $matches) === 1) {
            return $this->xmlName = $matches[1];
        }
        return $this->xmlName = $this->className;
    }

    /**
     * Получить ассоциативный массив, который кеширует соответствие полей и
     * их наименований в качестве XML элементов
     *
     * @return array
     */
    public function getPropertyXMLNameMap(): array
    {
        return $this->propertyXmlNameMap;
    }

    /**
     * Получить ассоциативный массив, который кеширует соответствие полей и
     * их наименований в качестве атрибутов XML элементов
     *
     * @return array
     */
    public function getPropertyXMLAttributeNameMap(): array
    {
        return $this->propertyXmlAttributeNameMap;
    }

    /**
     * Получить свойства класса
     *
     * @return ReflectionProperty[]
     */
    public function getProperties(): array
    {
        return $this->classProperties;
    }
}
