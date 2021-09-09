<?php
/**
 * Created by PhpStorm.
 * User: Dmitry Loshmanov
 * Date: 19.06.2020
 * Time: 16:48
 */

namespace Common\Packages\Application;

use Common\Base\Http\Request;

/**
 * Class Application
 * @package Common\App
 */
class Application extends \Laravel\Lumen\Application
{
    /**
     * Parse the incoming request and return the method and path info.
     *
     * @param  \Symfony\Component\HttpFoundation\Request|null  $request
     * @return array
     */
    protected function parseIncomingRequest($request)
    {
        if (! $request) {
            $request = Request::capture();
        }

        $this->instance(Request::class, $this->prepareRequest($request));

        return [$request->getMethod(), '/'.trim($request->getPathInfo(), '/')];
    }
}
