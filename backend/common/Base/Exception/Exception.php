<?php

namespace Common\Base\Exception;

use Throwable;

use Common\Base\Utils\TransformationUtils;

use Symfony\Component\HttpFoundation\Response;

/**
 * Class Exception
 * @package Common\Exception
 */
class Exception extends \Exception
{
    /**
     * HTTP-код
     */
    protected int $httpCode;

    /**
     * Код ошибки
     */
    protected ?string $exceptionCode = null;

    /**
     * Данные для ответа
     */
    protected ?array $payload = null;

    /**
     * Заголовки для ответа
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

    public function getHttpCode(): int
    {
        return $this->httpCode;
    }

    public function getExceptionCode(): ?string
    {
        return $this->exceptionCode;
    }

    public function setExceptionCode(?string $exceptionCode): self
    {
        $this->exceptionCode = $exceptionCode;
        return $this;
    }

    public function setPayload(?array $payload): Exception
    {
        $this->payload = $payload;
        return $this;
    }

    public function getPayload(): ?array
    {
        return $this->payload;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

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
        return (new self($message, Response::HTTP_UNAUTHORIZED, 0, $previous))
            ->setExceptionCode(__FUNCTION__);
    }

    /**
     * @param string $message
     * @param Throwable|null $previous
     * @return Exception
     */
    public static function forbidden(string $message = 'forbidden', Throwable $previous = null): self
    {
        return (new self($message, Response::HTTP_FORBIDDEN, 0, $previous))
            ->setExceptionCode(__FUNCTION__);
    }

    /**
     * @param string $message
     * @param Throwable|null $previous
     * @return Exception
     */
    public static function unprocessableEntity(string $message = 'unprocessable entity', Throwable $previous = null): self
    {
        return (new self($message, Response::HTTP_UNPROCESSABLE_ENTITY, 0, $previous))
            ->setExceptionCode(TransformationUtils::stringCamelCaseToUnderScore(__FUNCTION__));
    }

    /**
     * @param string $message
     * @param Throwable|null $previous
     * @return Exception
     */
    public static function badRequest(string $message = 'bad request', Throwable $previous = null): self
    {
        return (new self($message, Response::HTTP_BAD_REQUEST, 0, $previous))
            ->setExceptionCode(TransformationUtils::stringCamelCaseToUnderScore(__FUNCTION__));
    }

    /**
     * @param string $message
     * @param Throwable|null $previous
     * @return Exception
     */
    public static function internalServerError(string $message = 'internal server error', Throwable $previous = null): self
    {
        return (new self($message, Response::HTTP_INTERNAL_SERVER_ERROR, 0, $previous))
            ->setExceptionCode(TransformationUtils::stringCamelCaseToUnderScore(__FUNCTION__));
    }

    /**
     * @param string $message
     * @param Throwable|null $previous
     * @return Exception
     */
    public static function entityNotFoundException(string $message = 'entity not found', Throwable $previous = null): self
    {
        return (new self($message, Response::HTTP_NOT_FOUND, 0, $previous))
            ->setExceptionCode(TransformationUtils::stringCamelCaseToUnderScore(__FUNCTION__));
    }

    public static function serviceUnavailable($message = 'service unavailable', Throwable $previous = null): self
    {
        return (new self($message, Response::HTTP_SERVICE_UNAVAILABLE, 0, $previous))
            ->setExceptionCode(TransformationUtils::stringCamelCaseToUnderScore(__FUNCTION__));
    }

    public static function conflict($message = 'conflict with current state', Throwable $previous = null): self
    {
        return (new self($message,Response::HTTP_CONFLICT, 0, $previous))
            ->setExceptionCode(__FUNCTION__);
    }
}
