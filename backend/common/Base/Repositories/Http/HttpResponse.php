<?php

declare(strict_types=1);

namespace Common\Base\Repositories\Http;

class HttpResponse
{
    /** @var string[][] $headers */
    private array $headers = [];
    private $body = null;

    /**
     * @return string[][]
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @param string[][] $headers
     * @return static
     */
    public function setHeaders(array $headers): self
    {
        $this->headers = $headers;
        return $this;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function setBody($body): self
    {
        $this->body = $body;
        return $this;
    }
}
