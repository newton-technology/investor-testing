<?php

use Laravel\Lumen\Routing\Router;

/** @var Router $router */

$router->get('/tests', 'TestController@getTests');
$router->get('/tests/{id}', 'TestController@getTestItemById');
