<?php

declare(strict_types=1);

namespace Common\Base\Entities;

trait EntityAttributesTrait
{
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return EntityAttribute[]
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @param EntityAttribute[] $attributes
     * @return static
     */
    public function setAttributes(array $attributes): self
    {
        $this->attributes = $attributes;
        return $this;
    }

    public function getAttributeValueByKey(string $key): ?string
    {
        foreach ($this->getAttributes() as $attribute) {
            if ($attribute->getKey() === $key) {
                return $attribute->getValue();
            }
        }
        return null;
    }
}
