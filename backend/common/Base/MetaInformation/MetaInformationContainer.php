<?php

declare(strict_types=1);

namespace Common\Base\MetaInformation;

use ReflectionException;

/**
 * Контейнер для маппинга классов с их метаинформацией
 */
class MetaInformationContainer
{
    /**
     * Контейнер, в котором хранится информация о связи класса с его метаинформацией
     *
     * @var array<string, MetaInformation>
     */
    private static array $container = [];

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    /**
     * Получить сохраненный экземпляр метаинформации для класса
     *
     * Если его нет - сначала создаем
     *
     * @param string $className
     *
     * @return MetaInformation
     * @throws ReflectionException
     */
    public static function get(string $className): MetaInformation
    {
        if (!isset(self::$container[$className])) {
            self::$container[$className] = new MetaInformation($className);
        }

        return self::$container[$className];
    }
}
