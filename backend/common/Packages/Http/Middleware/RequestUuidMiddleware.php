<?php
/**
 * Created by PhpStorm.
 * User: Dmitry Loshmanov
 * Date: 13.08.2020
 * Time: 22:31
 */

namespace Common\Packages\Http\Middleware;

use Closure;
use Exception;

use Common\Base\Http\Headers;

use Ramsey\Uuid\Uuid;

/**
 * Class RequestUuid
 */
class RequestUuidMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Common\Base\Http\Request $request
     * @param Closure $next
     * @return mixed
     * @throws Exception
     */
    public function handle($request, Closure $next)
    {
        $requestId = $request->header(Headers::X_REQUEST_ID);
        if (empty($requestId)) {
            $request->headers->add(
                [
                    Headers::X_REQUEST_ID => method_exists(Uuid::class, 'uuid6') ? Uuid::uuid6() : Uuid::uuid4(),
                ]
            );
            $requestId = $request->header(Headers::X_REQUEST_ID);
        }

        $response = $next($request);
        header(Headers::X_REQUEST_ID . ':' . $requestId);

        return $response;
    }
}
