<?php

namespace Common\Base\Repositories\Http;

use Common\Base\Http\Headers;

/**
 * Trait HttpServiceRepositoryTrait
 * @package Common\Repositories\Traits
 *
 * Использовать только для запросов к внутренним сервисам,
 * при необходимости пробросить входящий токен авторизации в http-запрос
 */
trait HttpInternalRepositoryTrait
{
    use HttpRepositoryTrait;

    protected array $proxyHeaders = [Headers::X_REQUEST_ID, Headers::AUTHORIZATION];
}
