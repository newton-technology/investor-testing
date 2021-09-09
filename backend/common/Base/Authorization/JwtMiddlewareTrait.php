<?php

namespace Common\Base\Authorization;

use Closure;
use Throwable;

use Common\Base\Exception\Exception;

use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

trait JwtMiddlewareTrait
{
    public function __construct()
    {
        JWT::$leeway = config('authorization.jwt_leeway');
    }

    /**
     * @param Request $request
     * @param Closure $next
     * @return ResponseFactory|Response|null
     * @throws Exception
     */
    public function handle($request, Closure $next)
    {
        $jwt = $request->header('Authorization');
        if (substr($jwt, 0, 7) === 'Bearer ') {
            $jwt = substr($jwt, 7);
        } else {
            throw Exception::unauthorized('Access denied: invalid authorization header');
        }

        try {
            [1 => $payloadb64] = $this->getJwtChunks($jwt);
            $payload = JWT::jsonDecode(JWT::urlsafeB64Decode($payloadb64));

            $issuer = $this->validateIssuer($payload->iss);
            $this->validateAudience($request, $issuer, $payload->aud);

            $secret = $this->getSecret($issuer);
            $decodedToken = $this->validateToken($jwt, $secret);

            $sub = $this->validateUserIdentifier($decodedToken, $issuer);
            $boClientId = $this->getBoClientId($decodedToken, $issuer);

            $this->validateDecodedToken($sub, $decodedToken);

            $this->addAttributes($request, $sub, $decodedToken, $boClientId);

        } catch (\Exception $exception) {
            $message = 'invalid token';
            $headers = [];
            $payload = null;
            if ($exception instanceof Exception) {
                $message = $exception->getMessage();
                $payload = $exception->getPayload();
                $headers = $exception->getHeaders();
            }

            throw Exception::unauthorized($message, $exception)
                ->setPayload($payload)
                ->setHeaders($headers);
        }

        return $next($request);
    }

    protected function addAttributes($request, $sub, $decodedToken, $boClientId)
    {
        $request->attributes->add([
            'userId' => $sub,
            'decodedToken' => $decodedToken,
            'boClientId' => $boClientId
        ]);
    }

    /**
     * @param  string $jwt
     * @throws Exception
     * @return array [$headb64, $payloadb64, $cryptob64]
     */
    protected function getJwtChunks($jwt)
    {
        if (empty($jwt)) {
            throw Exception::unauthorized('Access denied: user token missing');
        }

        $chunks = explode('.', $jwt);
        return $chunks;
    }

    /**
     * @param array $issuer
     * @return false|string
     */
    protected function getSecret($issuer)
    {
        return file_get_contents($issuer['sslCert']);
    }

    /**
     * @param string $iss
     * @return array issuer config
     * @throws \Common\Base\Exception\Exception
     */
    protected function validateIssuer($iss)
    {
        if (!array_key_exists($iss, config('authorization.issuer'))) {
            throw Exception::unauthorized("Access denied: unknown issuer {$iss}");
        }

        $issuer = config('authorization.issuer')[$iss];
        if ($issuer['enabled'] === false) {
            throw Exception::unauthorized("Access denied: issuer \"{$iss}\" disabled");
        }
        if (!array_key_exists('sslCert', $issuer)) {
            throw Exception::unauthorized("Access denied: missing certificate for issuer {$iss}");
        }

        return $issuer;
    }

    /**
     * Проверка audience
     *
     * https://tools.ietf.org/html/rfc7519
     * https://tools.ietf.org/html/rfc8725
     *
     * Здесь мы можем проверить aud claim токена, этот claim определяет получателей для которых был выпущен токен.
     *
     * RFC8725: If the same issuer can issue JWTs that are intended for use by more
     * than one relying party or application, the JWT MUST contain an "aud"
     * (audience) claim that can be used to determine whether the JWT is
     * being used by an intended party or was substituted by an attacker at
     * an unintended party.
     * In such cases, the relying party or application MUST validate the
     * audience value, and if the audience value is not present or not
     * associated with the recipient, it MUST reject the JWT.
     *
     * Обычно Auth0 добавляет в aud ClientID, поэтому мы можем здесь проверить, что токен, который приходит
     * к нам выпущен именно для ClientID, который мы ожидаем. Пока это кажется избыточным, потому что на проде
     * у Auth0 только боевые клиенты и их все придется перечислить как разрешенные.
     *
     * Мы не можем полностью отказаться от проверок, так как используем сервисные токены для обращений
     * service->service. В случае выпуска токена сервис отправитель добавляет в aud целевой uri.
     * Роуты в которых возможны обращения service->service находятся за JwtIntensive, который проверяет
     * что роут разрешен для сервиса отправителя и что токен предназначен именно для роута в котором
     * выполняется проверка. Такие проверки не оставляют возможности использовать сервисные токены
     * для обращений в нецелевые роуты. Тем не менее, мы используем единый конфиг авторизации и при перехвате
     * сервисный токен может быть использован для обращений в роуты за JwtExtensive, чтобы избежать такой
     * атаки здесь мы дополнительно проверяем разрешено ли сервису выпустившему токен обращаться на любой роут
     * сервиса получателя. В конфигурации сервиса мы разрешаем ходить в любой роут для токенов auth0
     * (добавляем '*' в конфигурацию aud), но для токенов auth0 разрешаем только несколько целевых роутов за
     * JwtIntensive. Здесь мы отсеиваем попытки пойти с сервисным токеном в роут за JwtExtensive.
     *
     * @param Request $request
     * @param array $issuer
     * @param string $aud
     * @throws \Common\Base\Exception\Exception
     */
    protected function validateAudience($request, $issuer, $aud)
    {
        if (empty($issuer) || !array_key_exists('aud', $issuer) || empty($issuer['aud'])) {
            throw Exception::unauthorized('Access denied: audience for this client is empty');
        }

        if (in_array('*', $issuer['aud'])) {
            return;
        }

        $allowedRoutes = array_map(
            fn($aud) => $request->getSchemeAndHttpHost() . $aud,
            $issuer['aud']
        );

        if (!in_array($request->url(), $allowedRoutes)) {
            throw Exception::unauthorized(
                "Access denied: route forbidden, endpoint: \"{$request->getUri()}\""
            );
        }
    }

    /**
     * @param $jwt
     * @param $secret
     * @return object
     * @throws Exception
     */
    protected function validateToken($jwt, $secret)
    {
        try {
            $decoded = JWT::decode($jwt, $secret, ['RS256']);
        } catch (ExpiredException $exception) {
            throw Exception::unauthorized('Access denied: user token expired');
        } catch (BeforeValidException $exception) {
            throw Exception::unauthorized('Access denied: token before valid');
        } catch (\Exception $exception) {
            Log::error('Access denied: invalid token', ['token' => $jwt, 'err_message' => $exception->getMessage()]);
            throw Exception::unauthorized('Access denied: invalid token', $exception);
        }

        return $decoded;
    }

    /**
     * @param $sub
     * @param $decodedToken
     * @throws Throwable
     */
    protected function validateDecodedToken($sub, $decodedToken)
    {
        //
    }

    /**
     * @param $decodedToken
     * @param $issuer
     * @return mixed
     * @throws \Common\Base\Exception\Exception
     */
    protected function validateUserIdentifier($decodedToken, $issuer)
    {
        $sub = ['sub'];
        if (!empty($issuer['sub'] ?? null)) {
            $sub = $issuer['sub'];
        }

        foreach ($sub as $key) {
            if (property_exists($decodedToken, $key) && !empty($decodedToken->$key)) {
                return $decodedToken->$key;
            }
        }

        throw Exception::unauthorized('Access denied: user id is missing');
    }

    /**
     * @param $decodedToken
     * @param $issuer
     * @return string|null
     */
    protected function getBoClientId($decodedToken, $issuer): ?string
    {
        $boClientIdKey = 'bo_client_id';
        if (!empty($issuer['boClientId'] ?? null)) {
            $boClientIdKey = $issuer['boClientId'];
        }

        if (property_exists($decodedToken, $boClientIdKey) && !empty($decodedToken->$boClientIdKey)) {
            return $decodedToken->$boClientIdKey;
        }
        return null;
    }
}
