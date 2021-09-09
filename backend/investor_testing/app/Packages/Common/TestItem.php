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
     * Описание категории
     */
    protected CategoryItemCategory $category;

    /**
     * Список вопросов с ответами
     * @var TestItemQuestion[]
     */
    protected array $questions = [];

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return TestItem
     */
    public function setId(int $id): TestItem
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     * @return TestItem
     */
    public function setStatus(string $status): TestItem
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return int
     */
    public function getCreatedAt(): int
    {
        return $this->createdAt;
    }

    /**
     * @param int $createdAt
     * @return TestItem
     */
    public function setCreatedAt(int $createdAt): TestItem
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getUpdatedAt(): ?int
    {
        return $this->updatedAt;
    }

    /**
     * @param int|null $updatedAt
     * @return TestItem
     */
    public function setUpdatedAt(?int $updatedAt): TestItem
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * @return CategoryItemCategory
     */
    public function getCategory(): CategoryItemCategory
    {
        return $this->category;
    }

    /**
     * @param CategoryItemCategory $category
     * @return TestItem
     */
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

    /**
     * @param TestItemQuestion $question
     * @return TestItem
     */
    public function addQuestion(TestItemQuestion $question): TestItem
    {
        $this->questions[] = $question;
        return $this;
    }
}
