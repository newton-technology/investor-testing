<?php
/**
 * Created by PhpStorm.
 * User: Dmitry Loshmanov
 * Date: 26.08.2021
 * Time: 18:45
 */

namespace Newton\InvestorTesting\Packages\Common;

use Common\Base\Entities\ReflectionTrait;
use Common\Base\Entities\SerializableTrait;

class UserRole
{
    use ReflectionTrait;
    use SerializableTrait;

    public const ROLE_ADMIN = 'admin';

    protected int $id;
    protected int $userId;
    protected string $role;
    protected int $createdAt;
    protected ?int $updatedAt = null;

    /**
     * @return string[]
     */
    public static function getAvailableRoles(): array
    {
        return self::getConstants('ROLE_');
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): UserRole
    {
        $this->id = $id;
        return $this;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): UserRole
    {
        $this->userId = $userId;
        return $this;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function setRole(string $role): UserRole
    {
        $this->role = $role;
        return $this;
    }

    public function getCreatedAt(): int
    {
        return $this->createdAt;
    }

    public function setCreatedAt(int $createdAt): UserRole
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?int
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?int $updatedAt): UserRole
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }
}
