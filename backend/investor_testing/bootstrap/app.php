<?php
/** @noinspection PhpUnusedParameterInspection */
/** @noinspection DuplicatedCode */

require_once __DIR__ . '/../vendor/autoload.php';

(new Common\Packages\Application\LoadEnvironmentVariables(
    dirname(__DIR__)
))->bootstrap();

date_default_timezone_set(env('APP_TIMEZONE', 'UTC'));

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| Here we will load the environment and create the application instance
| that serves as the central piece of this framework. We'll use this
| application as an "IoC" container and router for this framework.
|
*/

$app = new Common\Packages\Application\Application(
    dirname(__DIR__)
);

$app->withFacades();

/*
|--------------------------------------------------------------------------
| Register Container Bindings
|--------------------------------------------------------------------------
|
| Now we will register a few bindings in the service container. We will
| register the exception handler and the console kernel. You may add
| your own bindings here if you like or you can make another file.
|
*/

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    Common\Base\Exception\Handler::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    Newton\InvestorTesting\Console\Kernel::class
);

/*
|--------------------------------------------------------------------------
| Register Config Files
|--------------------------------------------------------------------------
|
| Now we will register the "app" configuration file. If the file exists in
| your configuration directory it will be loaded; otherwise, we'll load
| the default version. You may register other files below as needed.
|
*/

$configs = [
    'app',
    'authorization',
    'broker',
    'database',
    'kafka',
    'logging',
    'mail',
];

foreach ($configs as $config) {
    $app->configure($config);
}

/*
|--------------------------------------------------------------------------
| Register Middleware
|--------------------------------------------------------------------------
|
| Next, we will register the middleware with the application. These can
| be global middleware that run before and after each request into a
| route or middleware that'll be assigned to some specific routes.
|
*/

$app->middleware(
    [
        Common\Packages\Http\Middleware\RequestUuidMiddleware::class,
    ]
);

/*
|--------------------------------------------------------------------------
| Register Service Providers
|--------------------------------------------------------------------------
|
| Here we will register all of the application's service providers which
| are used to bind services into the container. Service providers are
| totally optional, so you are not required to uncomment this line.
|
*/

$providers = [
    Common\Base\Http\RequestServiceProvider::class,
    Common\Base\Illuminate\Database\DatabaseServiceProvider::class,

    Fideloper\Proxy\TrustedProxyServiceProvider::class,
    Illuminate\Mail\MailServiceProvider::class,
];

foreach ($providers as $provider) {
    $app->register($provider);
}

/*
|--------------------------------------------------------------------------
| Register Plugins
|--------------------------------------------------------------------------
*/

$pluginsList = __DIR__ . '/../plugins/plugins.php';
if (file_exists($pluginsList)) {
    $plugins = require $pluginsList;
    foreach ($plugins as $plugin) {
        $app->register($plugin);
    }
}

/*
|--------------------------------------------------------------------------
| Load The Application Routes
|--------------------------------------------------------------------------
|
| Next we will include the routes file so that they can all be added to
| the application. This will provide all of the URLs the application
| can respond to, as well as the controllers that may handle them.
|
*/

$app->router->group(
    [
        'namespace' => 'Newton\InvestorTesting\Http\Controllers',
        'middleware' => [Fideloper\Proxy\TrustProxies::class],
    ],
    function ($router) {
        if (config('authorization.server.enabled')) {
            $router->group(
                ['prefix' => 'authorization'],
                function ($router) {
                    require __DIR__ . '/../routes/authorization.php';
                }
            );
        }

        $router->group(
            ['middleware' => [Common\Base\Authorization\JwtExtensiveMiddleware::class]],
            function ($router) {
                require __DIR__ . '/../routes/external.php';
            }
        );

        $router->group(
            [
                'namespace' => 'Management',
                'prefix' => 'management',
                'middleware' => Common\Base\Authorization\JwtScopeMiddleware::class . ':admin',
            ],
            function ($router) {
                require __DIR__ . '/../routes/management.php';
            }
        );

        require __DIR__ . '/../routes/support.php';
    }
);

return $app;
