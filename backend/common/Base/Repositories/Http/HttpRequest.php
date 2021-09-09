<?php

declare(strict_types=1);

namespace Common\Base\Repositories\Http;

class HttpRequest
{
    public const METHOD_GET = 'GET';
    public const METHOD_POST = 'POST';
    public const METHOD_PUT = 'PUT';
    public const METHOD_PATCH = 'PATCH';
    public const METHOD_DELETE = 'DELETE';

    /**
     * HTTP-метод запроса
     */
    private string $method;

    /**
     * Url, на который надо отправить запрос
     */
    private string $url;

    /**
     * Массив опций
     */
    private array $options = [];

    public function getMethod(): string
    {
        return $this->method;
    }

    public function setMethod(string $method): self
    {
        $this->method = $method;
        return $this;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;
        return $this;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function setOptions(array $options): self
    {
        $this->options = $options;
        return $this;
    }
}
