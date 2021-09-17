<?php

namespace Common\Base\Authorization;

use Closure;

use Common\Base\Exception\Exception;

use Illuminate\Http\Request;

class JwtLoginFlowMiddleware
{
    /**
     * @param Request $request
     * @param Closure $next
     * @param string $loginFlows
     *
     * @return Closure
     * @throws Exception
     */
    public function handle(
        Request $request,
        Closure $next,
        string $loginFlows
    ) {
        $decodedToken = $request->attributes->get('decodedToken');

        if (!$decodedToken) {
            throw Exception::unauthorized('Access denied: token not found');
        }

        if (!property_exists($decodedToken, 'login_flow')) {
            throw Exception::forbidden('Access denied: login flow not found');
        }

        $tokenLoginFlow = $decodedToken->login_flow;
        $allowedLoginFlows = explode('|', $loginFlows);

        if (!in_array(strtolower($tokenLoginFlow), $allowedLoginFlows)) {
            throw Exception::forbidden('Access denied: invalid login flow in token');
        }

        return $next($request);
    }
}
