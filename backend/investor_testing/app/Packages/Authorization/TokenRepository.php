<?php
/**
 * Created by PhpStorm.
 * User: Dmitry Loshmanov
 * Date: 03.08.2021
 * Time: 18:50
 */

namespace Newton\InvestorTesting\Packages\Authorization;

use DateTime;

use Newton\InvestorTesting\Packages\Common\User;

use Firebase\JWT\JWT;

class TokenRepository
{
    public const FLOW_SIGNIN = 'signin';
    public const FLOW_SIGNUP = 'signup';

    private ?string $publicKey = null;
    private ?string $privateKey = null;

    public static function getTokenIssuer(): string
    {
        return config('authorization.server.issuer');
    }

    public static function getAccessTokenAudience(): string
    {
        return config('authorization.server.issuer') . '/access';
    }

    public static function getRefreshTokenAudience(): string
    {
        return config('authorization.server.issuer') . '/refresh';
    }

    public static function getServiceTokenAudience(): string
    {
        return config('authorization.server.issuer') . '/service';
    }

    public function issueAccessToken(User $user, array $claims = []): string
    {
        return $this->issueToken(
            self::getAccessTokenAudience(),
            config('authorization.server.token.access.lifetime'),
            $user->getId(),
            $claims
        );
    }

    public function issueRefreshToken(User $user, array $claims = []): string
    {
        return $this->issueToken(
            self::getRefreshTokenAudience(),
            config('authorization.server.token.refresh.lifetime'),
            $user->getId(),
            $claims
        );
    }

    public function issueServiceToken(User $user, int $lifeTime, array $claims = []): string
    {
        return $this->issueToken(
            self::getServiceTokenAudience(),
            $lifeTime,
            $user->getId() ?? $user->getEmail(),
            $claims,
        );
    }

    public function decodeToken(string $token): object
    {
        return JWT::decode($token, $this->getPublicKey(), [config('authorization.server.token.algorithm')]);
    }

    protected function issueToken(string $audience, int $lifeTime, string $userIdentifier, array $claims = []): string
    {
        $now = (new DateTime())->getTimestamp();

        unset($claims['iat'], $claims['exp'], $claims['aud']);

        return JWT::encode(
            array_merge(
                [
                    'iss' => self::getTokenIssuer(),
                    'aud' => $audience,
                    'iat' => $now,
                    'exp' => $now + $lifeTime,
                    'sub' => $userIdentifier,
                ],
                $claims,
            ),
            $this->getPrivateKey(),
            config('authorization.server.token.algorithm')
        );
    }

    private function getPublicKey(): string
    {
        if ($this->publicKey === null) {
            $this->publicKey = file_get_contents(config('authorization.server.publicKey'));
        }
        return $this->publicKey;
    }

    private function getPrivateKey(): string
    {
        if ($this->privateKey === null) {
            $this->privateKey = file_get_contents(config('authorization.server.privateKey'));
        }
        return $this->privateKey;
    }
}
