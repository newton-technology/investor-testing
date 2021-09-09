<?php

namespace Newton\InvestorTesting\Packages\Common;

use Common\Base\Entities\SerializableTrait;

/**
 * Class TestQuestion
 * @package Newton\InvestorTesting\Packages\Common
 */
class TestQuestion
{
    use SerializableTrait;

    /**
     * Идентификатор вопроса
     */
    private int $id;

    /**
     * Идентификатор теста
     */
    private int $testId;

    /**
     * Идентификатор вопроса
     */
    private int $questionId;

    /**
     * Текст вопроса
     */
    private string $questionText;

    /**
     * Вес вопроса
     */
    private int $questionWeight;

    /**
     * Минимально допустимое к выбору количество ответов
     */
    private int $answersCountToChooseMin;

    /**
     * Максимально допустимое к выбору количество ответов
     */
    private ?int $answersCountToChooseMax;

    /**
     * Время создания записи
     */
    private int $createdAt;

    /**
     * Время изменения записи
     */
    private ?int $updatedAt = null;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getTestId(): int
    {
        return $this->testId;
    }

    public function setTestId(int $testId): self
    {
        $this->testId = $testId;
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

    public function getQuestionText(): string
    {
        return $this->questionText;
    }

    public function setQuestionText(string $questionText): self
    {
        $this->questionText = $questionText;
        return $this;
    }

    public function getQuestionWeight(): int
    {
        return $this->questionWeight;
    }

    public function setQuestionWeight(int $questionWeight): self
    {
        $this->questionWeight = $questionWeight;
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
     * @return int
     */
    public function getAnswersCountToChooseMin(): int
    {
        return $this->answersCountToChooseMin;
    }

    /**
     * @param int $answersCountToChooseMin
     * @return TestQuestion
     */
    public function setAnswersCountToChooseMin(int $answersCountToChooseMin): TestQuestion
    {
        $this->answersCountToChooseMin = $answersCountToChooseMin;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getAnswersCountToChooseMax(): ?int
    {
        return $this->answersCountToChooseMax;
    }

    /**
     * @param int|null $answersCountToChooseMax
     * @return TestQuestion
     */
    public function setAnswersCountToChooseMax(?int $answersCountToChooseMax): TestQuestion
    {
        $this->answersCountToChooseMax = $answersCountToChooseMax;
        return $this;
    }
}
