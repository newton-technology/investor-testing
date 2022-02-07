<?php

declare(strict_types=1);

namespace Common\Base\Repositories\Database;

use Throwable;

use Common\Base\Entities\EntityAttribute;

interface EntityAttributeRepositoryInterface
{
    /**
     * Получить атрибуты сущности по ID сущности
     *
     * @param int|string $entityId
     * @return EntityAttribute[]
     */
    public function getAttributesByEntityId($entityId): array;

    /**
     * Добавить новые атрибуты сущности
     *
     * @param EntityAttribute[] $entities
     * @throws Throwable
     */
    public function addAttributes(array $entities): void;

    /**
     * Обновить атрибуты сущности
     *
     * @param EntityAttribute[] $entities
     * @throws Throwable
     */
    public function addOrUpdateAttributes(array $entities): void;
}
