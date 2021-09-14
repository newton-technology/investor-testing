<?php
/**
 * Created by PhpStorm.
 * User: Dmitry Loshmanov
 * Date: 18.06.2020
 * Time: 18:51
 */

use Illuminate\Routing\Router;

/** @var Router $router */

$router->group(['prefix' => 'support', 'namespace' => '\Common\Packages\Http\Controllers'], function () use ($router) {
    $router->get('/version', 'SupportController@version');
    $router->get('/ping', 'SupportController@ping');
    $router->post('/ping', 'SupportController@ping');
});
