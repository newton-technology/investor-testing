<?php

use Laravel\Lumen\Routing\Router;

/** @var Router $router */

$router->get('/categories', 'CategoryController@getCategories');
$router->get('/categories/{id}', 'CategoryController@getCategory');
$router->get('/categories/codes/{code}', 'CategoryController@getCategoryByCode');
$router->post('/categories', 'CategoryController@addCategory');
$router->patch('/categories/{id:[0-9]+}/', 'CategoryController@editCategory');

$router->post('/tests', 'TestController@addTest');
$router->get('/tests/{id}', 'TestController@getTest');
$router->patch('/tests/{id}/answers', 'TestController@addTestAnswers');
