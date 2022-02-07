<?php

namespace Common\Base\Repositories\Http;

use Throwable;

use Common\Base\Http\Headers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Promise\Utils;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\RequestOptions;
use GuzzleHttp\TransferStats;
use Illuminate\Support\Facades\Log;
use Psr\Http\Message\ResponseInterface;

trait HttpRepositoryTrait
{
    protected ?HttpRepositoryLogger $requestLogger = null;

    public function setRequestLogger(HttpRepositoryLogger $requestLogger): self
    {
        $this->requestLogger = $requestLogger;
        return $this;
    }

    /**
     * @param string $uri
     * @param array $options
     * @return mixed
     * @throws GuzzleException
     */
    protected function postRequest(string $uri, array $options = [])
    {
        return $this->sendRequest(HttpRequest::METHOD_POST, $uri, $options);
    }

    /**
     * @param string $uri
     * @param array $options
     * @return mixed
     * @throws GuzzleException
     */
    protected function patchRequest(string $uri, array $options = [])
    {
        return $this->sendRequest(HttpRequest::METHOD_PATCH, $uri, $options);
    }

    /**
     * @param string $uri
     * @param array $options
     * @return mixed
     * @throws GuzzleException
     */
    protected function getRequest(string $uri, array $options = [])
    {
        return $this->sendRequest(HttpRequest::METHOD_GET, $uri, $options);
    }

    /**
     * @param string $uri
     * @param array $options
     * @return mixed
     * @throws GuzzleException
     */
    protected function deleteRequest(string $uri, array $options = [])
    {
        return $this->sendRequest(HttpRequest::METHOD_DELETE, $uri, $options);
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array $options
     * @return mixed
     * @throws GuzzleException
     */
    protected function sendRequest(string $method, string $uri, array $options = [])
    {
        return $this->sendRequestWithClientOptions($method, $uri, $options, $this->getClientOptions());
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array $options
     * @param array $clientOptions
     * @return array|bool|float|int|mixed|object|string|null
     * @throws GuzzleException
     */
    protected function sendRequestWithClientOptions(
        string $method,
        string $uri,
        array $options = [],
        array $clientOptions = []
    ) {
        $response = (new Client($clientOptions))->send(
            new Request($method, $uri),
            $this->optionsMiddleware($options)
        );

        return $this->getBodyContent($response, $options[RequestOptions::HEADERS]['Accept'] ?? null);
    }

    /**
     * Отправить несколько HTTP-запросов асинхронно
     *
     * @param HttpRequest[] $httpRequests
     *
     * @return array
     * @throws Throwable
     */
    public function sendAsyncRequests(array $httpRequests): array
    {
        return $this->sendAsyncRequestsWithClientOptions($httpRequests, $this->getClientOptions());
    }

    /**
     * Отправить запросы асинхронно
     *
     * @param HttpRequest[] $httpRequests
     * @param array $clientOptions
     *
     * @return array
     * @throws Throwable
     */
    protected function sendAsyncRequestsWithClientOptions(array $httpRequests, array $clientOptions = []): array
    {
        $promises = $this->getAsyncRequestsPromises($httpRequests, $clientOptions);
        $responses = Utils::unwrap($promises);
        return $this->getResponsesFromAsyncRequestsMap($httpRequests, $responses);
    }

    protected function getAsyncRequestsPromises(array $httpRequests, array $clientOptions = []): array
    {
        $client = new Client($clientOptions);
        $promises = [];
        foreach ($httpRequests as $name => $request) {
            $promises[$name] = $client->sendAsync(
                new Request($request->getMethod(), $request->getUrl()),
                $this->optionsMiddleware($request->getOptions())
            );
        }
        return $promises;
    }

    protected function getResponsesFromAsyncRequestsMap(array $httpRequests, array $responses): array
    {
        $result = [];
        foreach ($responses as $name => $response) {
            $result[$name] = $this->getBodyContent(
                $response,
                $httpRequests[$name]->getOptions()[RequestOptions::HEADERS]['Accept'] ?? null
            );
        }
        return $result;
    }

    protected function getClientOptions(): array
    {
        if (empty($logger = $this->requestLogger)) {
            Log::warning('missing request logger for ' . static::class);
        } else {
            $clientOptions[RequestOptions::ON_STATS] = function (TransferStats $stats) use ($logger) {
                $logger->log($stats->getRequest(), $stats->getResponse(), ['stats' => $stats]);
            };
        }

        return $clientOptions ?? [];
    }

    /**
     * @param array $options
     * @return array
     */
    protected function optionsMiddleware(array $options = []): array
    {
        return $this->headersMiddleware($options);
    }

    /**
     * @param array $options
     * @return array
     */
    protected function headersMiddleware(array $options = []): array
    {
        if (!function_exists('request')) {
            return $options;
        }
        $existsHeaders = [];
        foreach ($options[RequestOptions::HEADERS] ?? [] as $name => $value) {
            $existsHeaders[strtolower($name)] = $value;
        }
        $proxyHeaders = property_exists($this, 'proxyHeaders')
            ? $this->proxyHeaders ?? []
            : [Headers::X_REQUEST_ID];
        $output = $options[RequestOptions::HEADERS] ?? [];
        foreach ($proxyHeaders as $header) {
            if (!array_key_exists(strtolower($header), $existsHeaders)) {
                if (!is_null($input = request()->header($header))) {
                    $output[$header] = $input;
                }
            }
        }

        $options[RequestOptions::HEADERS] = $output;
        return $options;
    }

    /**
     * @param ResponseInterface $response
     * @param string|null $acceptHeader
     *
     * @return mixed
     */
    private function getBodyContent(ResponseInterface $response, string $acceptHeader = null)
    {
        if (empty($response->getBody()->getSize())) {
            return null;
        }

        if ($acceptHeader === 'application/xml') {
            return $response->getBody()->getContents();
        }

        return \GuzzleHttp\json_decode($response->getBody());
    }
}
