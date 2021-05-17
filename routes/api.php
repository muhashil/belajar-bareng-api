<?php

/** @var \Laravel\Lumen\Routing\Router $router */

$router->group(['prefix' => 'api'], function ($router) {
    // auth
    $router->post('/auth/register', 'AuthController@register');
    $router->post('/auth/login', 'AuthController@login');
    
    // route with auth
    $router->group(['middleware' => 'auth'], function ($router) {
        $router->get('/hello', 'HelloController@index');
        
        $router->post('/auth/logout', 'AuthController@logout');
        $router->post('/auth/refresh', 'AuthController@refresh');
    });
});
