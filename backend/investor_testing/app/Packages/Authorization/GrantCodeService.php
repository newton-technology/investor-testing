<?php
/**
 * Created by PhpStorm.
 * User: Dmitry Loshmanov
 * Date: 04.08.2021
 * Time: 18:41
 */

namespace Newton\InvestorTesting\Packages\Authorization;

use Throwable;

use Common\Base\Exception\Exception;
use Common\Base\Http\Request;
use Newton\InvestorTesting\Packages\Common\User;
use Newton\InvestorTesting\Packages\Common\UserRepository;

class GrantCodeService implements Grant
{
    private CodeRepository $codeRepository;
    private TokenRepository $tokenRepository;
    private UserRepository $userRepository;

    private int $codeLength = 6;
    private int $codeLifetime = 600;
    private int $codeAttemptsMax = 5;

    public function __construct(
        CodeRepository $codeRepository,
        TokenRepository $tokenRepository,
        UserRepository $userRepository
    ) {
        $this->codeRepository = $codeRepository;
        $this->tokenRepository = $tokenRepository;
        $this->userRepository = $userRepository;
    }

    public function applyProperties(array $properties)
    {
        $this->codeLength = $properties['code']['length'];
        $this->codeLifetime = $properties['code']['lifetime'];
        $this->codeAttemptsMax = $properties['code']['attempts_max'];
    }

    /**
     * @throws Throwable
     */
    public function validate(string $flow, Request $request)
    {
        $input = $request->toArray();
        switch ($flow) {
            case self::FLOW_SIGNUP:
            case self::FLOW_SIGNIN:
                if (!array_key_exists('email', $input)) {
                    throw Exception::unauthorized('invalid parameters for grant code');
                }
                break;
            case self::FLOW_TOKEN:
                if (!isset($input['code'], $input['access_token'])) {
                    throw Exception::unauthorized('invalid parameters for grant code');
                }
                break;
            default:
                throw Exception::unauthorized('invalid flow for grant code');
        }
    }

    /**
     * @throws Throwable
     */
    public function response(string $flow, Request $request): Response
    {
        $input = $request->toArray();
        switch ($flow) {
            case self::FLOW_SIGNUP:
                return $this->responseSignup($input);
            case self::FLOW_SIGNIN:
                return $this->responseSignin($input);
            case self::FLOW_TOKEN:
                return $this->responseToken($input);
            default:
                throw Exception::unauthorized('invalid flow for grant code');
        }
    }

    /**
     * @throws Throwable
     */
    public function responseSignup(array $input): TokenResponse
    {
        $email = $input['email'];
        $user = $this->userRepository->getUserByEmail($email);
        if (!empty($user)) {
            throw Exception::unauthorized('invalid username or password');
        }

        $user = (new User())->setEmail($email);
        $codeInfo = $this->codeRepository->issueCode($user, $this->codeLength, $this->codeLifetime);

        return (new TokenResponse())
            ->setAccessToken(
                $this->tokenRepository->issueServiceToken(
                    $user,
                    $this->codeLifetime,
                    ['uuid' => $codeInfo->getUuid(), 'flow' => TokenRepository::FLOW_SIGNUP]
                )
            );
    }

    /**
     * @throws Throwable
     */
    public function responseSignin(array $input): TokenResponse
    {
        $email = $input['email'];
        $user = $this->userRepository->getUserByEmail($email);
        if (empty($user)) {
            return $this->responseSignup($input);
        }

        $codeInfo = $this->codeRepository->issueCode($user, $this->codeLength, $this->codeLifetime);

        return (new TokenResponse())
            ->setAccessToken(
                $this->tokenRepository->issueServiceToken(
                    $user,
                    $this->codeLifetime,
                    ['uuid' => $codeInfo->getUuid(), 'flow' => TokenRepository::FLOW_SIGNIN]
                )
            );
    }

    /**
     * @throws Throwable
     */
    public function responseToken(array $input): TokenResponse
    {
        $decodedToken = $this->tokenRepository->decodeToken($input['access_token']);
        if (($decodedToken->aud ?? null) !== TokenRepository::getServiceTokenAudience()) {
            throw Exception::unauthorized('invalid audience for grant code');
        }

        $code = $input['code'];

        switch ($decodedToken->flow ?? null) {
            case TokenRepository::FLOW_SIGNUP:
                return $this->responseTokenSignup(
                    $code,
                    $decodedToken
                );
            case TokenRepository::FLOW_SIGNIN:
                return $this->responseTokenSignin(
                    $code,
                    $decodedToken
                );
            default:
                throw Exception::unauthorized('invalid flow');
        }
    }

    /**
     * @throws Throwable
     */
    protected function responseTokenSignup(
        string $code,
        object $tokenPayload
    ): TokenResponse {
        $user = (new User())->setEmail($tokenPayload->sub);

        $codeInfo = $this->codeRepository->tryToUseCode($user, $tokenPayload->uuid, $code);
        if (empty($codeInfo)) {
            throw Exception::forbidden('code not found');
        }

        if (!$codeInfo->isVerified()) {
            $this->responseTokenError($codeInfo);
        }

        $this->userRepository->addUser($user);

        return $this->responseTokenSuccess($user);
    }

    /**
     * @throws Throwable
     */
    protected function responseTokenSignin(
        string $code,
        object $tokenPayload
    ): TokenResponse {
        $user = $this->userRepository->getUserById($tokenPayload->sub);

        $codeInfo = $this->codeRepository->tryToUseCode($user, $tokenPayload->uuid, $code);
        if (empty($codeInfo)) {
            throw Exception::forbidden('code not found');
        }

        if (!$codeInfo->isVerified()) {
            $this->responseTokenError($codeInfo);
        }

        return $this->responseTokenSuccess($user);
    }

    protected function responseTokenSuccess(User $user): TokenResponse
    {
        return (new TokenResponse())
            ->setAccessToken($this->tokenRepository->issueAccessToken($user))
            ->setRefreshToken($this->tokenRepository->issueRefreshToken($user));
    }

    /**
     * @throws Throwable
     */
    protected function responseTokenError(CodeInfo $codeInfo)
    {
        $attemptsLeft = $this->codeAttemptsMax - $codeInfo->getAttemptsCount();
        if ($attemptsLeft <= 0) {
            $this->codeRepository->revokeCode($codeInfo->getUuid());
        }

        throw Exception::unprocessableEntity()
            ->setPayload(
                [
                    'expires' => $codeInfo->getExpiredAt(),
                    'nextRetryTime' => $codeInfo->getExpiredAt(),
                    'attemptsLeft' => $attemptsLeft,
                ]
            );
    }
}
