<?php

namespace Common\Packages\Http\Middleware;

use Closure;
use Exception;

use Common\Base\Http\Response;
use Common\Base\Repositories\Redis\RedisRepositoryCacheTrait;
use Common\Base\Utils\Composer;

use Illuminate\Http\Request;

class ThrottleMiddleware
{
    use RedisRepositoryCacheTrait;

    /**
     * @var string
     */
    protected $connection = 'cache';

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param string $configPrefix
     * @return mixed
     * @throws Exception
     */
    public function handle(Request $request, Closure $next, string $configPrefix)
    {
        $interval = config($configPrefix . '.interval');
        $attemptsCount = config($configPrefix . '.attempts');

        $key = implode(
            ':',
            [
                'throttling',
                Composer::getApplicationName(),
                $request->getMethod(),
                $request->getPathInfo(),
            ]
        );

        $accessAttempts = $this->incr($key);
        if ($accessAttempts === 1) {
            $this->expire($key, $interval);
        }
        if ($accessAttempts > $attemptsCount) {
            return Response::tooManyRequests([], [Response::HEADER_REQUEST_TIME_LEFT => $this->ttl($key)]);
        }

        $response = $next($request);
        $response->headers->set(Response::HEADER_REQUEST_TIME_LEFT, $this->ttl($key));
        return $response;
    }
}
