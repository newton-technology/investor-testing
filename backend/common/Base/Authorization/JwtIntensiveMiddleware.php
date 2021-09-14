<?php

namespace Common\Base\Authorization;

use Common\Base\Exception\Exception;

use Illuminate\Http\Request;

/**
 * Class JwtIntensive
 * @package Common\Http\Middleware
 */
class JwtIntensiveMiddleware
{
    use JwtMiddlewareTrait;

    /**
     * @param Request $request
     * @param array $issuer
     * @param string $aud
     * @throws Exception
     */
    protected function validateAudience($request, $issuer, $aud)
    {
        if (empty($issuer) || !array_key_exists('aud', $issuer) || empty($issuer['aud'])) {
            throw Exception::unauthorized('Access denied: audience for this client is empty');
        }

        if (!$this->validateAllowedRoutes($request, $issuer['aud'])) {
            throw Exception::unauthorized(
                "Access denied: route forbidden (route: \"{$request->url()}\")"
            );
        }

        $audiences = is_array($aud) ? $aud : [$aud];

        if (!in_array($request->url(), $audiences)) {
            throw Exception::unauthorized(
                "Access denied: audience forbidden (audience: \""
                . implode(',', $audiences) . "\", endpoint: \"{$request->url()}\")"
            );
        }
    }

    /**
     * Проверка совпадения урла с доступными роутами
     *
     * @param Request $request
     * @param array $audiences Доступные роуты
     * @return bool
     */
    protected function validateAllowedRoutes(Request $request, array $audiences): bool
    {
        $allowedRoutes = array_map(
            fn($aud) => $request->getSchemeAndHttpHost() . $aud,
            $audiences
        );

        return
            in_array($request->url(), $allowedRoutes)
            || in_array(
                1,
                array_map(
                    fn($aud) => preg_match("~^{$aud}$~", $request->getPathInfo()),
                    $audiences
                )
            );
    }
}
