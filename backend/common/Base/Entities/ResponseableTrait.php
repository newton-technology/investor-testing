<?php

namespace Common\Base\Entities;

use ReflectionException;

/**
 * Trait ResponseableTrait
 * @package Common\Entities\Traits
 */
trait ResponseableTrait
{
    use SerializableTrait;

    /**
     * @param string[] $fields
     * @param bool $withNullFields
     * @return array
     * @throws ReflectionException
     */
    public function toResponse(array $fields = [], bool $withNullFields = true): array
    {
        if (empty($fields)) {
            $fields = $this->getMetaInformation($this)->getResponseableFields();
        }

        return $this->toArray(
            $fields,
            $withNullFields,
            false,
            [$this, 'toResponseConverter'],
        );
    }

    /**
     * @param $value
     * @param bool $withNullFields
     * @param bool $convertToUnderScore
     * @return mixed
     * @throws ReflectionException
     */
    protected function toResponseConverter(
        $value,
        bool $withNullFields = true,
        bool $convertToUnderScore = false
    ) {
        if (!is_object($value)) {
            return $this->fieldsConverter($value, $withNullFields, $convertToUnderScore);
        }

        $itemMetaInformation = $this->getMetaInformation($value);
        if ($itemMetaInformation->classHasToResponseMethod()) {
            return $value->toResponse(
                [],
                $withNullFields,
                $convertToUnderScore,
            );
        }

        if ($itemMetaInformation->classHasToArrayMethod()) {
            return $value->toArray(
                [],
                $withNullFields,
                $convertToUnderScore
            );
        }

        return $this->objectToArray($value, [], $withNullFields, $convertToUnderScore, [$this, 'toResponseConverter']);
    }
}
