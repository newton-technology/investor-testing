<?php
/**
 * Created by PhpStorm.
 * User: Dmitry Loshmanov
 * Date: 22.09.2021
 * Time: 10:56
 */

namespace Newton\InvestorTesting\Packages\Management;

class TestItem extends \Newton\InvestorTesting\Packages\Common\TestItem
{
    protected int $userId;
    protected ?string $userEmail;

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): TestItem
    {
        $this->userId = $userId;
        return $this;
    }

    public function getUserEmail(): ?string
    {
        return $this->userEmail;
    }

    public function setUserEmail(?string $userEmail): TestItem
    {
        $this->userEmail = $userEmail;
        return $this;
    }
}
