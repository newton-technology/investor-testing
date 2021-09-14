<?php

namespace Common\Base\Authorization;

use Closure;

use Common\Base\Exception\Exception;

/**
 * Class JwtIntensive
 * @package Common\Http\Middleware
 */
class JwtScopeMiddleware
{
    use JwtMiddlewareTrait {
        handle as traitHandle;
    }

    /**
     * @var string[]
     */
    protected array $scopes = [];

    public function handle($request, Closure $next, string ...$scopes)
    {
        $this->scopes = $scopes;
        return $this->traitHandle($request, $next);
    }

    protected function validateDecodedToken($sub, $decodedToken)
    {
        $scopes = explode(' ', $decodedToken->scope);
        foreach ($this->scopes as $scope) {
            if (!in_array($scope, $scopes)) {
                throw Exception::unauthorized('Access denied: invalid scopes in token');
            }
        }
    }
}
