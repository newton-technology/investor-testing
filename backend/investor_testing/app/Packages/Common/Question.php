<?php

namespace Newton\InvestorTesting\Packages\Common;

use Common\Base\Entities\ReflectionTrait;
use Common\Base\Entities\SerializableTrait;

/**
 * Class Question
 * @package Newton\InvestorTesting\Packages\Common
 */
class Question
{
    use SerializableTrait;
    use ReflectionTrait;

    public const GROUP_CODE_EVALUATION = 'evaluation';
    public const GROUP_CODE_CUSTOM = 'custom';
    public const GROUP_CODE_KNOWLEDGE = 'knowledge';

    public const STATUS_ENABLED = 'enabled';
    public const STATUS_DISABLED = 'disabled';

    /**
     * Идентификатор вопроса
     */
    private int $id;

    /**
     * Код блока вопросов
     */
    private string $groupCode;

    /**
     * Идентификатор категории
     */
    private int $categoryId;

    /**
     * Текст вопроса
     */
    private string $text;

    /**
     * Минимальное количество предлагаемых вариантов ответа
     */
    private int $answersCountMin = 4;

    /**
     * Максимальное количество предлагаемых вариантов ответа
     */
    private int $answersCountMax = 4;

    /**
     * Минимально допустимое к выбору количество ответов
     */
    private int $answersCountToChooseMin = 1;

    /**
     * Максимально допустимое к выбору количество ответов
     */
    private ?int $answersCountToChooseMax = 1;

    /**
     * Вес вопроса
     */
    private int $weight = 0;

    /**
     * Статус вопроса (`enabled` - может быть предложен; `disabled` - не может быть предложен)
     */
    private string $status = self::STATUS_ENABLED;

    /**
     * Время создания записи
     */
    private int $createdAt;

    /**
     * Время изменения записи
     */
    private ?int $updatedAt = null;

    /**
     * @return string[]
     */
    public static function getAvailableGroupCodes(): array
    {
        return self::getConstants('GROUP_CODE_');
    }

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

    public function getGroupCode(): string
    {
        return $this->groupCode;
    }

    public function setGroupCode(string $groupCode): self
    {
        $this->groupCode = $groupCode;
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

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;
        return $this;
    }

    public function getAnswersCountMin(): int
    {
        return $this->answersCountMin;
    }

    public function setAnswersCountMin(int $answersCountMin): self
    {
        $this->answersCountMin = $answersCountMin;
        return $this;
    }

    public function getAnswersCountMax(): int
    {
        return $this->answersCountMax;
    }

    public function setAnswersCountMax(int $answersCountMax): self
    {
        $this->answersCountMax = $answersCountMax;
        return $this;
    }

    public function getAnswersCountToChooseMin(): int
    {
        return $this->answersCountToChooseMin;
    }

    public function setAnswersCountToChooseMin(int $answersCountToChooseMin): self
    {
        $this->answersCountToChooseMin = $answersCountToChooseMin;
        return $this;
    }

    public function getAnswersCountToChooseMax(): ?int
    {
        return $this->answersCountToChooseMax;
    }

    public function setAnswersCountToChooseMax(?int $answersCountToChooseMax): self
    {
        $this->answersCountToChooseMax = $answersCountToChooseMax;
        return $this;
    }

    public function getWeight(): int
    {
        return $this->weight;
    }

    public function setWeight(int $weight): self
    {
        $this->weight = $weight;
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
