<?php
/**
 * Created by PhpStorm.
 * User: Dmitry Loshmanov
 * Date: 03.08.2021
 * Time: 18:47
 */

namespace Newton\InvestorTesting\Http\Controllers;

use Throwable;

use Common\Base\Http\Request;
use Common\Base\Http\Response;
use Newton\InvestorTesting\Packages\Authorization\Grant;
use Newton\InvestorTesting\Packages\Authorization\GrantFactory;

use Illuminate\Http\JsonResponse;

class AuthorizationController extends Controller
{
    private GrantFactory $grantFactory;

    public function __construct(GrantFactory $grantFactory)
    {
        $this->grantFactory = $grantFactory;
    }

    /**
     * @throws Throwable
     */
    public function signup(Request $request): JsonResponse
    {
        return $this->response(Grant::FLOW_SIGNUP, $request);
    }

    /**
     * @throws Throwable
     */
    public function signin(Request $request): JsonResponse
    {
        return $this->response(Grant::FLOW_SIGNIN, $request);
    }

    /**
     * @throws Throwable
     */
    public function token(Request $request): JsonResponse
    {
        return $this->response(Grant::FLOW_TOKEN, $request);
    }

    /**
     * @throws Throwable
     */
    private function response(string $flow, Request $request): JsonResponse
    {
        $validatedInput = $this->validate(
            $request,
            [
                'grant_type' => 'required|string',
            ]
        );

        $grant = $this->grantFactory->fromName($validatedInput['grant_type']);
        $grant->validate($flow, $request);

        return Response::success(
            $grant->response($flow, $request)->toResponse()
        );
    }
}
