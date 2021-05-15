<?php

/** @var \Laravel\Lumen\Routing\Router $router */

$router->group(['prefix' => 'api'], function ($router) {
    $router->get('/hello', 'HelloController@index');

    // auth
    $router->post('/auth/register', 'AuthController@register');
});
