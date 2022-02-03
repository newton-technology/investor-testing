<?php

declare(strict_types=1);

namespace Common\Base\Entities;

class Attribute
{
    /**
     * Ключ атрибута
     */
    protected string $key;

    /**
     * Значение атрибута
     */
    protected string $value;

    public function getKey(): string
    {
        return $this->key;
    }

    public function setKey(string $key): self
    {
        $this->key = $key;
        return $this;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;
        return $this;
    }
}
