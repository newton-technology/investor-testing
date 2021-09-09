<?php
/**
 * Created by PhpStorm.
 * User: Dmitry Loshmanov
 * Date: 04.08.2021
 * Time: 19:32
 */

namespace Newton\InvestorTesting\Packages\Authorization;

use Throwable;
use UnexpectedValueException;

use Common\Base\Exception\Exception;
use Common\Base\Http\Request;
use Newton\InvestorTesting\Packages\Common\User;

class GrantRefreshService implements Grant
{
    private TokenRepository $tokenRepository;

    public function __construct(TokenRepository $tokenRepository)
    {
        $this->tokenRepository = $tokenRepository;
    }

    public function applyProperties(array $properties)
    {
        //
    }

    /**
     * @throws Throwable
     */
    public function validate(string $flow, Request $request)
    {
        if ($flow !== self::FLOW_TOKEN) {
            throw Exception::unauthorized('invalid flow for grant refresh');
        }

        if ($request->input('refresh_token') === null) {
            throw Exception::unauthorized('invalid parameters for grant refresh');
        }
    }

    /**
     * @throws Throwable
     */
    public function response(string $flow, Request $request): Response
    {
        try {
            $decodedToken = $this->tokenRepository->decodeToken($request->input('refresh_token'));
        } catch (UnexpectedValueException $exception) {
            throw Exception::unauthorized($exception->getMessage());
        }

        if ($decodedToken->iss !== TokenRepository::getTokenIssuer()) {
            throw Exception::unauthorized('invalid issuer for grant refresh');
        }
        if ($decodedToken->aud !== TokenRepository::getRefreshTokenAudience()) {
            throw Exception::unauthorized('invalid audience for grant refresh');
        }

        $user = (new User())->setId($decodedToken->sub);
        $claims = (array)$decodedToken;

        return (new TokenResponse())
            ->setAccessToken($this->tokenRepository->issueAccessToken($user, $claims))
            ->setRefreshToken($this->tokenRepository->issueRefreshToken($user, $claims));
    }
}
