<?php

namespace Common\Packages\Http\Middleware;

use Common\Base\Http\Request;
use Common\Base\Utils\Composer;

class UserThrottleMiddleware extends ThrottleMiddleware
{
    protected function getKey(Request $request): string
    {
        return implode(
            ':',
            array_filter([
                'throttling',
                Composer::getApplicationName(),
                $request->getMethod(),
                $request->getPathInfo(),
                $request->getUserId(),
            ])
        );
    }
}
