<?php
/**
 * Created by PhpStorm.
 * User: Dmitry Loshmanov
 * Date: 19.06.2020
 * Time: 17:02
 */

namespace Common\Base\Http;

use Illuminate\Support\ServiceProvider;

class RequestServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(\Common\Base\Http\Request::class, function () {
            return \Common\Base\Http\Request::capture();
        });

        $this->app->singleton(\Illuminate\Http\Request::class, function () {
            return app(\Common\Base\Http\Request::class);
        });
    }
}
