<?php
/**
 * Created by PhpStorm.
 * User: Dmitry Loshmanov
 * Date: 26.08.2021
 * Time: 18:28
 */

namespace Newton\InvestorTesting\Packages\Authorization;

use Throwable;

use Common\Base\Exception\Exception;
use Common\Base\Http\Request;
use Newton\InvestorTesting\Packages\Common\UserRepository;
use Newton\InvestorTesting\Packages\Common\UserRole;
use Newton\InvestorTesting\Packages\Common\UserRoleRepository;

use Illuminate\Hashing\HashManager;

class GrantPasswordService implements Grant
{
    protected HashManager $hashManager;
    protected TokenRepository $tokenRepository;
    protected UserRepository $userRepository;
    protected UserRoleRepository $userRoleRepository;

    public function __construct(
        HashManager $hashManager,
        TokenRepository $tokenRepository,
        UserRepository $userRepository,
        UserRoleRepository $userRoleRepository
    ) {
        $this->hashManager = $hashManager;
        $this->tokenRepository = $tokenRepository;
        $this->userRepository = $userRepository;
        $this->userRoleRepository = $userRoleRepository;
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
        if ($flow !== self::FLOW_SIGNIN) {
            throw Exception::unauthorized('invalid flow for grant password');
        }

        if (empty($request->input('username')) || empty($request->input('password'))) {
            throw Exception::unauthorized('invalid parameters for grant password');
        }

        if (!empty($request->input('scope')) && !is_string($request->input('scope'))) {
            throw Exception::unauthorized('invalid scopes for grant password');
        }
    }

    /**
     * @throws Throwable
     */
    public function response(string $flow, Request $request): Response
    {
        $user = $this->userRepository->getUserByEmail($request->input('username'));
        if ($user === null
            || empty($user->getPassword())
            || !$this->hashManager->check($request->input('password'), $user->getPassword())
        ) {
            throw Exception::unauthorized('invalid username or password');
        }

        $claims = [];
        if ($request->input('scope') !== null) {
            $scopes = explode(' ', $request->input('scope'));
            if (!$this->userRoleRepository
                ->userRolesExists(
                    array_map(fn($scope) => (new UserRole())->setUserId($user->getId())->setRole($scope), $scopes)
                )) {
                throw Exception::unauthorized('scopes are prohibited');
            }

            $claims['scope'] = implode(' ', $scopes ?? []);
        }

        return (new TokenResponse())
            ->setAccessToken($this->tokenRepository->issueAccessToken($user, $claims))
            ->setRefreshToken($this->tokenRepository->issueRefreshToken($user, $claims));
    }
}
