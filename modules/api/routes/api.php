<?php

/** @var Router $router */

use Laravel\Lumen\Routing\Router;

$router->group(['prefix' => 'api/user'], function () use ($router) {
    $router->post('/auth', 'UserController@auth');
});
