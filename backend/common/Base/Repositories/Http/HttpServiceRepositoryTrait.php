<?php
/**
 * Created by PhpStorm.
 * User: Dmitry Loshmanov
 * Date: 26.08.2020
 * Time: 11:16
 */

namespace Common\Base\Repositories\Http;

use Common\Base\Jwt\JWTRepository;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\RequestOptions;
use GuzzleHttp\TransferStats;
use Illuminate\Support\Facades\Log;

/**
 * Trait HttpServiceRepositoryTrait
 * @package Common\Repositories\Traits
 *
 * Использовать только для запросов к внутренним сервисам,
 * при необходимости выпускать сервисный токен
 */
trait HttpServiceRepositoryTrait
{
    use HttpRepositoryTrait;

    private JWTRepository $jwtRepository;

    public function __construct(JWTRepository $jwtRepository)
    {
        $this->jwtRepository = $jwtRepository;
    }

    /**
     * @param string $userId
     * @param string $uri
     * @param array $options
     * @param array $claims
     * @return mixed
     * @throws GuzzleException
     */
    protected function postRequest(string $userId, string $uri, array $options = [], array $claims = [])
    {
        return $this->sendRequest($userId, 'POST', $uri, $options, $claims);
    }

    /**
     * @param string $userId
     * @param string $uri
     * @param array $options
     * @param array $claims
     * @return mixed
     * @throws GuzzleException
     */
    protected function patchRequest(string $userId, string $uri, array $options = [], array $claims = [])
    {
        return $this->sendRequest($userId, 'PATCH', $uri, $options, $claims);
    }

    /**
     * @param string $userId
     * @param string $uri
     * @param array $options
     * @param array $claims
     * @return mixed
     * @throws GuzzleException
     */
    protected function deleteRequest(string $userId, string $uri, array $options = [], array $claims = [])
    {
        return $this->sendRequest($userId, 'DELETE', $uri, $options, $claims);
    }

    /**
     * @param string $userId
     * @param string $uri
     * @param array $options
     * @param array $claims
     * @return mixed
     * @throws GuzzleException
     */
    protected function getRequest(string $userId, string $uri, array $options = [], array $claims = [])
    {
        return $this->sendRequest($userId, 'GET', $uri, $options, $claims);
    }

    /**
     * @param string $userId
     * @param string $method
     * @param string $uri
     * @param array $options
     * @param array $claims
     * @return mixed
     * @throws GuzzleException
     */
    protected function sendRequest(string $userId, string $method, string $uri, array $options = [], array $claims = [])
    {
        $clientOptions = [
            RequestOptions::HEADERS => [
                'Authorization' => 'Bearer ' .
                    $this->jwtRepository->issueToken($userId, $this->getAudience($uri), $claims),
            ],
        ];

        if (empty($logger = $this->requestLogger)) {
            Log::warning('missing request logger for ' . static::class);
        } else {
            $clientOptions[RequestOptions::ON_STATS] = function (TransferStats $stats) use ($logger) {
                $logger->log($stats->getRequest(), $stats->getResponse(), ['stats' => $stats]);
            };
        }

        $response = (new Client($clientOptions))->send(
            new Request($method, $uri),
            $this->optionsMiddleware($options)
        );

        return !empty($response->getBody()->getSize())
            ? \GuzzleHttp\json_decode($response->getBody())
            : null;
    }

    /**
     * @param string $endpoint
     * @return string
     */
    protected function getAudience(string $endpoint): string
    {
        return $endpoint;
    }

    /**
     * @param string $target
     * @param string $endpoint
     * @param string|null $audience
     * @return string
     */
    protected function transformAudience(string $target, string $endpoint, ?string $audience): string
    {
        return empty($audience)
            ? $target
            : preg_replace(
                '/^(' . preg_quote($endpoint, '/') . ')(.*)$/',
                "{$audience}$2",
                $target
            );
    }

    /**
     * @param string $userId
     * @param string $audience
     * @param array $claims
     * @return string
     */
    protected function issueToken(string $userId, string $audience, array $claims = []): string
    {
        return $this->jwtRepository->issueToken($userId, $audience, $claims);
    }
}
