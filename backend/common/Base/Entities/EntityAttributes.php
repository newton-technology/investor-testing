<?php

declare(strict_types=1);

namespace Common\Base\Entities;

interface EntityAttributes
{
    /**
     * @return int|string
     */
    public function getId();

    /**
     * @return EntityAttribute[]
     */
    public function getAttributes(): array;

    /**
     * @param EntityAttribute[] $attributes
     * @return self
     */
    public function setAttributes(array $attributes): self;
}
