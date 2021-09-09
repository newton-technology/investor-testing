<?php
/**
 * Created by PhpStorm.
 * User: Dmitry Loshmanov
 * Date: 04.08.2021
 * Time: 18:42
 */

namespace Newton\InvestorTesting\Packages\Authorization;

use Common\Base\Entities\ResponseableTrait;

class TokenResponse implements Response
{
    use ResponseableTrait;

    private string $accessToken;
    private ?string $refreshToken = null;

    public function toResponse(): array
    {
        return $this->toArray([], false);
    }

    /**
     * @return string
     */
    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    /**
     * @param string $accessToken
     * @return TokenResponse
     */
    public function setAccessToken(string $accessToken): TokenResponse
    {
        $this->accessToken = $accessToken;
        return $this;
    }

    /**
     * @return string
     */
    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }

    /**
     * @param string $refreshToken
     * @return TokenResponse
     */
    public function setRefreshToken(string $refreshToken): TokenResponse
    {
        $this->refreshToken = $refreshToken;
        return $this;
    }
}
