<?php

namespace Common\Base\Entities;

use ReflectionClass;
use ReflectionProperty;

use Common\Base\Utils\TransformationUtils;

/**
 * Trait ReflectionTrait
 * @package Common\Entities\Traits
 */
trait ReflectionTrait
{
    /**
     * @param string $prefix
     * @return array
     */
    public static function getConstants(string $prefix = ''): array
    {
        return array_filter(
            (new ReflectionClass(self::class))->getConstants(),
            function ($const) use ($prefix) {
                return substr($const, 0, strlen($prefix)) === $prefix;
            },
            ARRAY_FILTER_USE_KEY
        );
    }

    /**
     * @param string $prefix
     * @return array
     */
    public static function getConstantNames(string $prefix = '')
    {
        return array_flip(self::getConstants($prefix));
    }

    /**
     * @param int $filter
     * @return ReflectionProperty[]
     */
    public static function getProperties(int $filter = null): array {
        return (new ReflectionClass(self::class))->getProperties($filter);
    }

    /**
     * @param int|null $filter
     * @param bool $convertToUnderScore
     * @return string[]
     */
    public static function getPropertiesNames(int $filter = null, bool $convertToUnderScore = false) {
        return array_map(
            function ($property) use ($convertToUnderScore) {
                return $convertToUnderScore
                    ? TransformationUtils::stringCamelCaseToUnderScore($property->getName())
                    : $property->getName();
            },
            self::getProperties($filter)
        );
    }
}
