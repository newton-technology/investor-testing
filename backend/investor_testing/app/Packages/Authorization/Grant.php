<?php
/**
 * Created by PhpStorm.
 * User: Dmitry Loshmanov
 * Date: 04.08.2021
 * Time: 18:42
 */

namespace Newton\InvestorTesting\Packages\Authorization;

use Common\Base\Http\Request;

interface Grant
{
    public const FLOW_SIGNUP = 'signup';
    public const FLOW_SIGNIN = 'signin';
    public const FLOW_TOKEN = 'token';

    public function applyProperties(array $properties);
    public function validate(string $flow, Request $request);
    public function response(string $flow, Request $request): Response;
}
