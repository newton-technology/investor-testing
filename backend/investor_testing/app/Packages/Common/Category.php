<?php

namespace Newton\InvestorTesting\Packages\Common;

use Common\Base\Entities\ReflectionTrait;
use Common\Base\Entities\ResponseableTrait;

/**
 * Class Category
 * @package Newton\InvestorTesting\Packages\Common
 */
class Category
{
    use ResponseableTrait;
    use ReflectionTrait;

    public const STATUS_ENABLED = 'enabled';
    public const STATUS_DISABLED = 'disabled';

    /**
     * Идентификатор категории
     */
    protected int $id;

    /**
     * Код категории
     */
    protected ?string $code = null;

    /**
     * Имя категории
     */
    protected string $name;

    /**
     * Описание категории
     */
    protected ?string $description = null;

    /**
     * Краткое описание категории
     */
    protected ?string $descriptionShort = null;

    /**
     * Время создания записи
     */
    protected int $createdAt;

    /**
     * Время изменения записи
     */
    protected ?int $updatedAt = null;

    /**
     * Статус категории (`enabled` - категория доступна; `disabled` - категория недоступна)
     */
    protected ?string $status = self::STATUS_DISABLED;

    /**
     * @return string[]
     */
    public static function getAvailableStatuses(): array
    {
        return self::getConstants('STATUS_');
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): self
    {
        $this->code = $code;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getDescriptionShort(): ?string
    {
        return $this->descriptionShort;
    }

    public function setDescriptionShort(?string $descriptionShort): Category
    {
        $this->descriptionShort = $descriptionShort;
        return $this;
    }

    public function getCreatedAt(): int
    {
        return $this->createdAt;
    }

    public function setCreatedAt(int $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?int
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?int $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;
        return $this;
    }
}
