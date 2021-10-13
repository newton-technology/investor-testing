<?php
/**
 * Created by PhpStorm.
 * User: Dmitry Loshmanov
 * Date: 19.06.2020
 * Time: 16:22
 */

namespace Common\Base\Http;

/**
 * Class Request
 * @package Common\Http
 */
class Request extends \Laravel\Lumen\Http\Request
{
    public const CLAIM_CLIENT_ID = 'bo_client_id';

    private const CLAIM_NEWTON_AUTH_FLOW = 'login_flow';
    private const NEWTON_AUTH_FLOW_WITH_MFA = ['NORMAL', 'NORMAL_WITH_EMAIL'];

    /**
     * @var string|null
     */
    private $auditUserId;

    /**
     * @return string|null
     */
    public function getRequestId(): ?string
    {
        return $this->header(Headers::X_REQUEST_ID);
    }

    /**
     * @return string|null
     */
    public function getAuditUserId(): ?string
    {
        return $this->getUserId() ?? $this->auditUserId;
    }

    /**
     * @param string|null $auditUserId
     * @return Request
     */
    public function setAuditUserId(?string $auditUserId): Request
    {
        $this->auditUserId = $auditUserId;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getUserId(): ?int
    {
        $attribute = $this->attributes->get('userId');
        return ctype_digit((string)$attribute) ? $attribute : null;
    }

    /**
     * @return string|null
     */
    public function getClientId(): ?string
    {
        return $this->attributes->get('boClientId');
    }

    /**
     * @return string
     */
    public function getAuthServerUserId(): string
    {
        return $this->attributes->get('decodedToken')->sub;
    }

    public function getTokenPayload(): ?object
    {
        return $this->attributes->get('decodedToken');
    }

    /**
     * @return string|null
     */
    public function getToken(): ?string
    {
        $raw = $this->header('authorization');
        if (empty($raw) || substr($raw, 0, 7) !== 'Bearer ') {
            return null;
        }

        return substr($raw, 7);
    }

    /**
     * Проверяем признак включенной 2-хфакторной аутентификации
     * @return bool
     */
    public function doesMfaEnabledInToken(): bool
    {
        if (in_array(
            $this->attributes->get('decodedToken')->{self::CLAIM_NEWTON_AUTH_FLOW} ?? null,
            self::NEWTON_AUTH_FLOW_WITH_MFA
        )) {
            return true;
        }
        return false;
    }
}
