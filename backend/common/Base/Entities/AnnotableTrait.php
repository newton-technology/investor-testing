<?php

declare(strict_types=1);

namespace Common\Base\Entities;

use ReflectionException;

/**
 * Трейт для обработки сущности с использованием phpDoc-аннотаций
 */
trait AnnotableTrait
{
    use ResponseableTrait {
        toResponse as toTraitResponse;
    }

    /**
     * Преобразовать сущность в массив, пригодный для отдачи в ответ
     *
     * В процессе преобразования применяются обработчики, указанные в phpDoc у свойств объекта
     *
     * @param string[] $fields
     * @param bool $withNullFields
     *
     * @return array
     * @throws ReflectionException
     */
    public function toResponse(array $fields = [], bool $withNullFields = true): array
    {
        return $this->processingResponseDataMiddleware(
            $this->toTraitResponse($fields, $withNullFields)
        );
    }

    /**
     * Обработать ответ для API
     *
     * @param array $responseData
     *
     * @return array
     * @throws ReflectionException
     */
    private function processingResponseDataMiddleware(array $responseData): array
    {
        $phpDocProperties = $this->decodeDocBlockForProperties($this);

        foreach (array_keys($responseData) as $objectProperty) {
            if (isset($phpDocProperties[$objectProperty]['excludeFromResponse'])) {
                unset($responseData[$objectProperty]);

                continue;
            }

            if (isset($phpDocProperties[$objectProperty]['responseName'])) {
                $responseData[$phpDocProperties[$objectProperty]['responseName']] = $responseData[$objectProperty];
                unset($responseData[$objectProperty]);
            }
        }

        return $responseData;
    }

    /**
     * Декодировать элементы phpDoc-блока для каждого свойства
     *
     * @param object $object
     *
     * @return array
     * @throws ReflectionException
     */
    private function decodeDocBlockForProperties(object $object): array
    {
        $properties = $this->getMetaInformation($object)->getProperties();
        foreach ($properties as $property) {
            $docBlock = $property->getDocComment();
            if ($docBlock === false) {
                continue;
            }

            // Распарсим все элементы doc-блока, убрав замыкающие символы "*/"
            preg_match_all('/\*\s*@(\w*)\s*(.*?)\n/', mb_substr($docBlock, 0, -2), $matches, PREG_SET_ORDER);

            foreach ($matches as $match) {
                /**
                 * $match[1] содержит имя phpDoc-параметра
                 * $match[2] содержит полное значение для phpDoc-параметра
                 */
                $phpDocProperties[$property->getName()][trim($match[1])] = trim($match[2]);
            }
        }

        return $phpDocProperties ?? [];
    }
}
