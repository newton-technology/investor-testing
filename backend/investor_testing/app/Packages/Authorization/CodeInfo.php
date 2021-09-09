<?php
/**
 * Created by PhpStorm.
 * User: Dmitry Loshmanov
 * Date: 03.08.2021
 * Time: 21:10
 */

namespace Newton\InvestorTesting\Packages\Authorization;

use Common\Base\Entities\SerializableTrait;

class CodeInfo
{
    use SerializableTrait;

    private string $uuid;
    private string $hash;
    private bool $verified = false;
    private int $attemptsCount = 0;
    private int $expiredAt;

    /**
     * @return string
     */
    public function getUuid(): string
    {
        return $this->uuid;
    }

    /**
     * @param string $uuid
     * @return CodeInfo
     */
    public function setUuid(string $uuid): CodeInfo
    {
        $this->uuid = $uuid;
        return $this;
    }

    /**
     * @return string
     */
    public function getHash(): string
    {
        return $this->hash;
    }

    /**
     * @param string $hash
     * @return CodeInfo
     */
    public function setHash(string $hash): CodeInfo
    {
        $this->hash = $hash;
        return $this;
    }

    /**
     * @return bool
     */
    public function isVerified(): bool
    {
        return $this->verified;
    }

    /**
     * @param bool $verified
     * @return CodeInfo
     */
    public function setVerified(bool $verified): CodeInfo
    {
        $this->verified = $verified;
        return $this;
    }

    /**
     * @return int
     */
    public function getAttemptsCount(): int
    {
        return $this->attemptsCount;
    }

    /**
     * @param int $attemptsCount
     * @return CodeInfo
     */
    public function setAttemptsCount(int $attemptsCount): CodeInfo
    {
        $this->attemptsCount = $attemptsCount;
        return $this;
    }

    /**
     * @return CodeInfo
     */
    public function incrementAttemptsCount(): CodeInfo
    {
        $this->attemptsCount++;
        return $this;
    }

    /**
     * @return int
     */
    public function getExpiredAt(): int
    {
        return $this->expiredAt;
    }

    /**
     * @param int $expiredAt
     * @return CodeInfo
     */
    public function setExpiredAt(int $expiredAt): CodeInfo
    {
        $this->expiredAt = $expiredAt;
        return $this;
    }
}
