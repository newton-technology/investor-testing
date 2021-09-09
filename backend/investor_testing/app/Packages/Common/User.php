<?php

namespace Newton\InvestorTesting\Packages\Common;

use Common\Base\Entities\SerializableTrait;

/**
 * Class User
 * @package Newton\InvestorTesting\Packages\Common
 */
class User
{
    use SerializableTrait;

    /**
     * Идентификатор пользователя
     */
    private ?int $id = null;

    /**
     * Адрес электронной почты
     */
    private string $email;

    /**
     * Хэш пароля пользователя
     */
    private ?string $password = null;

    /**
     * Время создания записи
     */
    private int $createdAt;

    /**
     * Время изменения записи
     */
    private ?int $updatedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): User
    {
        $this->password = $password;
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
