<?php

namespace Plugins\Example\ExampleRoute;

use Plugins\Plugin;

class ExampleRoutePlugin extends Plugin
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/config/example.php', 'example');
    }

    public function boot()
    {
        $this->app->router->group(
            [
                'namespace' => 'Plugins\Example\ExampleRoute\App\Http\Controllers',
            ],
            function ($router) {
                require __DIR__ . '/routes/example.php';
            }
        );
    }
}
