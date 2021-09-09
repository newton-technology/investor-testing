<?php

namespace Newton\InvestorTesting\Packages\Common;

use Common\Base\Entities\ReflectionTrait;
use Common\Base\Entities\SerializableTrait;

/**
 * Class Answer
 * @package Newton\InvestorTesting\Packages\Common
 */
class Answer
{
    use SerializableTrait;
    use ReflectionTrait;

    public const STATUS_ENABLED = 'enabled';
    public const STATUS_REQUIRED = 'required';
    public const STATUS_DISABLED = 'disabled';

    /**
     * Идентификатор ответа
     */
    protected int $id;

    /**
     * Идентификатор вопроса
     */
    protected int $questionId;

    /**
     * Текст ответа
     */
    protected string $text;

    /**
     * Признак правильности ответа
     */
    protected ?bool $correct;

    /**
     * Вес при сортировке предлагаемых вариантов ответа (от меньшего к большему)
     */
    protected int $sort = 0;

    /**
     * Статус ответа (`enabled` - может быть предложен; `required` - должен быть предложен; `disabled` - не может быть предложен)
     */
    protected string $status = self::STATUS_ENABLED;

    /**
     * Время создания записи
     */
    protected int $createdAt;

    /**
     * Время изменения записи
     */
    protected ?int $updatedAt = null;

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

    public function getQuestionId(): int
    {
        return $this->questionId;
    }

    public function setQuestionId(int $questionId): self
    {
        $this->questionId = $questionId;
        return $this;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;
        return $this;
    }

    public function isCorrect(): ?bool
    {
        return $this->correct;
    }

    public function setCorrect(?bool $correct): self
    {
        $this->correct = $correct;
        return $this;
    }

    public function getSort(): int
    {
        return $this->sort;
    }

    public function setSort(int $sort): self
    {
        $this->sort = $sort;
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
}
