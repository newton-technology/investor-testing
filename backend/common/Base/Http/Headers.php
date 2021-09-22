<?php
/**
 * Created by PhpStorm.
 * User: Dmitry Loshmanov
 * Date: 23.08.2021
 * Time: 19:12
 */

namespace Common\Base\Http;

class Headers
{
    public const AUTHORIZATION = 'Authorization';

    public const X_CACHE_UPDATE = 'x-cache-update';
    public const X_YIELD_TRACE = 'x-yield-trace';
    public const X_REQUEST_ID = 'X-Request-ID';

    public const X_TOKEN_REVOKED = 'X-Token-Revoked';

    public const X_LIST_LIMIT = 'X-List-Limit';
    public const X_LIST_OFFSET = 'X-List-Offset';
    public const X_LIST_TOTAL = 'X-List-Total';
}
