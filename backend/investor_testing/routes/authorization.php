<?php
/**
 * Created by PhpStorm.
 * User: Dmitry Loshmanov
 * Date: 03.08.2021
 * Time: 20:23
 */

/** @var Router $router */

use Laravel\Lumen\Routing\Router;

$router->post('/signup', 'AuthorizationController@signup');
$router->post('/signin', 'AuthorizationController@signin');
$router->post('/token', 'AuthorizationController@token');

