<?php

namespace Common\Base\Jwt;

use DateTime;
use RuntimeException;

use Firebase\JWT\JWT;

/**
 * Class JWTRepository
 */
class JWTRepository
{
    /**
     * @var string публичный ключ
     */
    private $publicKey;

    /**
     * @var string приватный ключ
     */
    private $privateKey;

    /**
     * Время жизни токена
     * @var int
     */
    private $tokenLifetime = 300;

    /**
     * JWTRepository constructor.
     * @param string $publicKey путь к публичному ключу
     * @param string $privateKey путь к приватному ключу
     * @param string|null $privateKeyPassphrase пароль приватного ключа
     */
    public function __construct(string $publicKey, string $privateKey, ?string $privateKeyPassphrase = null)
    {
        $this->publicKey = file_get_contents($publicKey);
        $this->privateKey = openssl_pkey_get_private(file_get_contents($privateKey), $privateKeyPassphrase ?? '');
    }

    public function getTokenLifetime(): int
    {
        return $this->tokenLifetime;
    }

    public function setTokenLifetime(int $tokenLifetime): JWTRepository
    {
        $this->tokenLifetime = $tokenLifetime;
        return $this;
    }

    public function issueToken(string $userId, string $audience, array $claims = [], int $lifeTime = null): string
    {
        $now = (new DateTime())->getTimestamp();
        $lifeTime = $lifeTime ?? $this->tokenLifetime;
        return JWT::encode(
            array_merge(
                $claims,
                [
                    'aud' => $audience,
                    'exp' => $now + $lifeTime,
                    'iat' => $now,
                    'iss' => $claims['iss'] ?? $this->getIssuer(),
                    'sub' => $userId,
                ]
            ),
            $this->privateKey,
            'RS256'
        );
    }

    public function decodeToken(string $token): object
    {
        return JWT::decode($token, $this->publicKey);
    }

    public function getTokenPayload(string $jwt): object
    {
        if (empty($jwt)) {
            throw new RuntimeException('bad jwt token');
        }

        [1 => $payloadb64] = explode('.', $jwt);

        return JWT::jsonDecode(JWT::urlsafeB64Decode($payloadb64));
    }

    protected function getIssuer(): string
    {
        $composer = json_decode(file_get_contents(base_path('composer.json')));
        return $composer->name;
    }
}
