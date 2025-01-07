<?php

/** @var \Laravel\Lumen\Routing\Router $router */

use App\Http\Controllers\PostsController;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->get('/hello', function () {
    return 'hello';
});



$router->group(['prefix' => 'api'], function () use ($router) {
    $router->post('/register', 'AuthController@register');

    $router->post('/login', 'AuthController@login');
});


$router->group(['prefix' => 'api', 'middleware' => 'auth'], function () use ($router) {

    $router->get('/posts', 'PostsController@index');
    $router->get('/posts/{id}', 'PostsController@show');

    $router->get('/posts/{id}', 'PostsController@update');

    $router->post('/posts', 'PostsController@store');
    $router->delete('/posts/{id}', 'PostsController@destroy');
});
