<?php

use Laravel\Lumen\Routing\Router;

/** @var Router $router */

$router->get('/tests', 'TestController@getTests');
