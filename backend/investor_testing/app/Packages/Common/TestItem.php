<?php
/**
 * Created by PhpStorm.
 * User: Dmitry Loshmanov
 * Date: 06.08.2021
 * Time: 18:47
 */

namespace Newton\InvestorTesting\Packages\Common;

use Common\Base\Entities\ResponseableTrait;

class TestItem
{
    use ResponseableTrait;

    /**
     * Идентификатор теста
     */
    protected int $id;

    /**
     * Статус теста
     */
    protected string $status;

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
     * Описание категории
     */
    protected CategoryItemCategory $category;

    /**
     * Список вопросов с ответами
     * @var TestItemQuestion[]
     */
    protected array $questions = [];

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): TestItem
    {
        $this->id = $id;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): TestItem
    {
        $this->status = $status;
        return $this;
    }

    public function getCreatedAt(): int
    {
        return $this->createdAt;
    }

    public function setCreatedAt(int $createdAt): TestItem
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?int
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?int $updatedAt): TestItem
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getCompletedAt(): ?int
    {
        return $this->completedAt;
    }

    public function setCompletedAt(?int $completedAt): TestItem
    {
        $this->completedAt = $completedAt;
        return $this;
    }

    public function getCategory(): CategoryItemCategory
    {
        return $this->category;
    }

    public function setCategory(CategoryItemCategory $category): TestItem
    {
        $this->category = $category;
        return $this;
    }

    /**
     * @return TestItemQuestion[]
     */
    public function getQuestions(): array
    {
        return $this->questions;
    }

    /**
     * @param TestItemQuestion[] $questions
     * @return TestItem
     */
    public function setQuestions(array $questions): TestItem
    {
        $this->questions = $questions;
        return $this;
    }

    public function addQuestion(TestItemQuestion $question): TestItem
    {
        $this->questions[] = $question;
        return $this;
    }
}
