<?php
/**
 * Created by PhpStorm.
 * User: Dmitry Loshmanov
 * Date: 27.08.2021
 * Time: 23:00
 */

namespace Newton\InvestorTesting\Packages\Management;

use Common\Base\Entities\ResponseableTrait;

class TestListItem
{
    use ResponseableTrait;

    protected int $id;
    protected int $createdAt;
    protected ?int $updatedAt = null;
    protected string $status;
    protected int $userId;
    protected ?string $userEmail = null;
    protected TestListCategory $category;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): TestListItem
    {
        $this->id = $id;
        return $this;
    }

    public function getCreatedAt(): int
    {
        return $this->createdAt;
    }

    public function setCreatedAt(int $createdAt): TestListItem
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?int
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?int $updatedAt): TestListItem
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): TestListItem
    {
        $this->status = $status;
        return $this;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): TestListItem
    {
        $this->userId = $userId;
        return $this;
    }

    public function getUserEmail(): ?string
    {
        return $this->userEmail;
    }

    public function setUserEmail(?string $userEmail): TestListItem
    {
        $this->userEmail = $userEmail;
        return $this;
    }

    public function getCategory(): TestListCategory
    {
        return $this->category;
    }

    public function setCategory(TestListCategory $category): TestListItem
    {
        $this->category = $category;
        return $this;
    }
}
