<?php

declare(strict_types=1);

namespace Common\Base\Entities;

abstract class EntityAttribute extends Attribute
{
    /**
     * @return int|string
     */
    abstract public function getEntityId();

    /**
     * @param int|string $entityId
     * @return self
     */
    abstract public function setEntityId($entityId): self;
}
