<?php
/**
 * Created by PhpStorm.
 * User: dloshmanov
 * Date: 2019-04-16
 * Time: 13:14
 */

namespace Common\Base\Exception;

use Throwable;

use Symfony\Component\HttpFoundation\Response;

/**
 * Class Exception
 * @package Common\Exception
 */
class Exception extends \Exception
{
    /**
     * @var int
     */
    protected int $httpCode;

    /**
     * @var array|null
     */
    protected ?array $payload = null;

    /**
     * @var array
     */
    protected array $headers = [];

    /**
     * Exception constructor.
     * @param string $message
     * @param int $httpCode
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(string $message = "", int $httpCode = 500, int $code = 0, Throwable $previous = null)
    {
        $this->httpCode = $httpCode;
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return int
     */
    public function getHttpCode(): int
    {
        return $this->httpCode;
    }

    /**
     * @param array|null $payload
     * @return Exception
     */
    public function setPayload(?array $payload): Exception
    {
        $this->payload = $payload;
        return $this;
    }

    /**
     * @return array|null
     */
    public function getPayload(): ?array
    {
        return $this->payload;
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @param array $headers
     * @return Exception
     */
    public function setHeaders(array $headers): self
    {
        $this->headers = $headers;
        return $this;
    }

    /**
     * @param string $message
     * @param Throwable|null $previous
     * @return Exception
     */
    public static function unauthorized(string $message = 'authorization error', Throwable $previous = null): self
    {
        return new self($message, Response::HTTP_UNAUTHORIZED, 0, $previous);
    }

    /**
     * @param string $message
     * @param Throwable|null $previous
     * @return Exception
     */
    public static function forbidden(string $message = 'forbidden', Throwable $previous = null): self
    {
        return new self($message, Response::HTTP_FORBIDDEN, 0, $previous);
    }

    /**
     * @param string $message
     * @param Throwable|null $previous
     * @return Exception
     */
    public static function unprocessableEntity(string $message = 'unprocessable entity', Throwable $previous = null): self
    {
        return new self($message, Response::HTTP_UNPROCESSABLE_ENTITY, 0, $previous);
    }

    /**
     * @param string $message
     * @param Throwable|null $previous
     * @return Exception
     */
    public static function badRequest(string $message = 'bad request', Throwable $previous = null): self
    {
        return new self($message, Response::HTTP_BAD_REQUEST, 0, $previous);
    }

    /**
     * @param string $message
     * @param Throwable|null $previous
     * @return Exception
     */
    public static function internalServerError(string $message = 'internal server error', Throwable $previous = null): self
    {
        return new self($message, Response::HTTP_INTERNAL_SERVER_ERROR, 0, $previous);
    }

    /**
     * @param string $message
     * @param Throwable|null $previous
     * @return Exception
     */
    public static function entityNotFoundException(string $message = 'entity not found', Throwable $previous = null): self
    {
        return new self($message, Response::HTTP_NOT_FOUND, 0, $previous);
    }

    public static function serviceUnavailable($message = 'service unavailable', Throwable $previous = null): self
    {
        return new self($message, Response::HTTP_SERVICE_UNAVAILABLE, 0, $previous);
    }

    public static function conflict($message = 'conflict with current state', Throwable $previous = null): self
    {
        return new self($message,Response::HTTP_CONFLICT, 0, $previous);
    }
}
