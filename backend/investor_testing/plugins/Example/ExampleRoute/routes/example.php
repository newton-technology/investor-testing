<?php

use Laravel\Lumen\Routing\Router;

/** @var Router $router */

$router->group(
    ['prefix' => 'example'],
    function () use ($router) {
        $router->get('hello', 'MessageController@getWelcomeMessage');
    }
);
