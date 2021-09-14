<?php

namespace Newton\InvestorTesting\Packages\Common;

use Common\Base\Entities\SerializableTrait;

/**
 * Class TestAnswer
 * @package Newton\InvestorTesting\Packages\Common
 */
class TestAnswer
{
    use SerializableTrait;

    /**
     * Идентификатор записи
     */
    private int $id;

    /**
     * Идентификатор вопроса в тесте
     */
    private int $testQuestionId;

    /**
     * Идентификатор ответа
     */
    private int $answerId;

    /**
     * Текст ответа
     */
    private string $answerText;

    /**
     * Признак выбора ответа
     */
    private bool $selected = false;

    /**
     * Признак правильности ответа
     */
    private ?bool $correct;

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

    public function getTestQuestionId(): int
    {
        return $this->testQuestionId;
    }

    public function setTestQuestionId(int $testQuestionId): self
    {
        $this->testQuestionId = $testQuestionId;
        return $this;
    }

    public function getAnswerId(): int
    {
        return $this->answerId;
    }

    public function setAnswerId(int $answerId): self
    {
        $this->answerId = $answerId;
        return $this;
    }

    public function getAnswerText(): string
    {
        return $this->answerText;
    }

    public function setAnswerText(string $answerText): self
    {
        $this->answerText = $answerText;
        return $this;
    }

    public function isSelected(): bool
    {
        return $this->selected;
    }

    public function setSelected(bool $selected): self
    {
        $this->selected = $selected;
        return $this;
    }

    public function isCorrect(): ?bool
    {
        return $this->correct;
    }

    public function setCorrect(?bool $correct): TestAnswer
    {
        $this->correct = $correct;
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
