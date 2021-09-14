<?php
/**
 * Created by PhpStorm.
 * User: Dmitry Loshmanov
 * Date: 27.07.2020
 * Time: 11:25
 */

namespace Common\Base\Authorization;

use Closure;
use Throwable;

/**
 * Class JwtFree
 * @package Common\Http\Middleware
 */
class JwtFreeMiddleware
{
    use JwtMiddlewareTrait {
        handle as traitHandle;
    }

    public function handle($request, Closure $next)
    {
        try {
            return $this->traitHandle($request, $next);
        } catch (Throwable $exception) {
            return $next($request);
        }
    }
}
