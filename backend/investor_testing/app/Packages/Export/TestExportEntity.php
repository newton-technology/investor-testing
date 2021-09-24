<?php
/**
 * Created by PhpStorm.
 * User: Dmitry Loshmanov
 * Date: 19.08.2021
 * Time: 18:57
 */

namespace Newton\InvestorTesting\Packages\Export;

use Common\Base\Entities\SerializableTrait;

class TestExportEntity
{
    use SerializableTrait;

    protected int $id;
    protected int $userId;
    protected ?string $email = null;
    protected ?string $category = null;
    protected ?string $status = null;
    protected int $completedAt;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): TestExportEntity
    {
        $this->id = $id;
        return $this;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): TestExportEntity
    {
        $this->userId = $userId;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): TestExportEntity
    {
        $this->email = $email;
        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(?string $category): TestExportEntity
    {
        $this->category = $category;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): TestExportEntity
    {
        $this->status = $status;
        return $this;
    }

    public function getCompletedAt(): int
    {
        return $this->completedAt;
    }

    public function setCompletedAt(int $completedAt): TestExportEntity
    {
        $this->completedAt = $completedAt;
        return $this;
    }
}
