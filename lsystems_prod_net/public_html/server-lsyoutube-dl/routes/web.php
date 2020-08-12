<?php

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
   return 'Web Wervice Realizado con LSCodeGenerator';
});

$router->group(['middleware' => []], function () use ($router) {
   $router->post('/download/info', ['uses' => 'DownloadController@info']);
   $router->post('/download/one', ['uses' => 'DownloadController@one']);
   $router->post('/download/multiple', ['uses' => 'DownloadController@multiple']);
});