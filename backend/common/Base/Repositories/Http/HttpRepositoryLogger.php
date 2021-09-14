<?php
/**
 * Created by PhpStorm.
 * User: Dmitry Loshmanov
 * Date: 20.08.2021
 * Time: 16:08
 */

namespace Common\Base\Repositories\Http;

interface HttpRepositoryLogger
{
    /**
     * @param $request
     * @param $response
     * @param array $params
     */
    public function log($request, $response, $params = []);
}
