<?php

namespace Newton\InvestorTesting\Packages\Common;

use Common\Base\Entities\ReflectionTrait;
use Common\Base\Entities\ResponseableTrait;

/**
 * Class Test
 * @package Newton\InvestorTesting\Packages\Common
 */
class Test
{
    use ResponseableTrait;
    use ReflectionTrait;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_FAILED = 'failed';
    public const STATUS_PASSED = 'passed';
    public const STATUS_CANCELED = 'canceled';

    /**
     * Идентификатор теста
     */
    protected int $id;

    /**
     * Идентификатор пользователя
     */
    protected int $userId;

    /**
     * Идентификатор категории
     */
    protected int $categoryId;

    /**
     * Статус теста
     */
    protected string $status = self::STATUS_DRAFT;

    /**
     * Время создания записи
     */
    protected int $createdAt;

    /**
     * Время изменения записи
     */
    protected ?int $updatedAt = null;

    /**
     * Время прохождения теста
     */
    protected ?int $completedAt = null;

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

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): self
    {
        $this->userId = $userId;
        return $this;
    }

    public function getCategoryId(): int
    {
        return $this->categoryId;
    }

    public function setCategoryId(int $categoryId): self
    {
        $this->categoryId = $categoryId;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
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

    /**
     * @return int|null
     */
    public function getCompletedAt(): ?int
    {
        return $this->completedAt;
    }

    /**
     * @param int|null $completedAt
     * @return Test
     */
    public function setCompletedAt(?int $completedAt): Test
    {
        $this->completedAt = $completedAt;
        return $this;
    }
}
