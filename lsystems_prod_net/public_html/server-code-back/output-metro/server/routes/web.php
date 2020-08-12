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
   $router->post('/login', ['uses' => 'AuthController@login']);
   $router->post('/register', ['uses' => 'AuthController@register']);
   $router->post('/password_recovery_request', ['uses' => 'AuthController@passwordRecoveryRequest']);
   $router->get('/password_recovery', ['uses' => 'AuthController@passwordRecovery']);
});

$router->group(['middleware' => ['auth']], function () use ($router) {
   $router->post('/user/password_change', ['uses' => 'AuthController@passwordChange']);


   //NETMONITOR

   //CRUD ProfilePicture
   $router->post('/profilepicture', ['uses' => 'ProfilePictureController@post']);
   $router->get('/profilepicture', ['uses' => 'ProfilePictureController@get']);
   $router->get('/profilepicture/paginate', ['uses' => 'ProfilePictureController@paginate']);
   $router->put('/profilepicture', ['uses' => 'ProfilePictureController@put']);
   $router->delete('/profilepicture', ['uses' => 'ProfilePictureController@delete']);

   //CRUD User
   $router->post('/user', ['uses' => 'UserController@post']);
   $router->get('/user', ['uses' => 'UserController@get']);
   $router->get('/user/paginate', ['uses' => 'UserController@paginate']);
   $router->put('/user', ['uses' => 'UserController@put']);
   $router->delete('/user', ['uses' => 'UserController@delete']);

   //CRUD Target
   $router->post('/target', ['uses' => 'TargetController@post']);
   $router->get('/target', ['uses' => 'TargetController@get']);
   $router->get('/target/paginate', ['uses' => 'TargetController@paginate']);
   $router->get('/target/backup', ['uses' => 'TargetController@backup']);
   $router->put('/target', ['uses' => 'TargetController@put']);
   $router->delete('/target', ['uses' => 'TargetController@delete']);
   $router->post('/target/masive_load', ['uses' => 'TargetController@masiveLoad']);

   //CRUD Statistic
   $router->post('/statistic', ['uses' => 'StatisticController@post']);
   $router->get('/statistic', ['uses' => 'StatisticController@get']);
   $router->get('/statistic/paginate', ['uses' => 'StatisticController@paginate']);
   $router->get('/statistic/backup', ['uses' => 'StatisticController@backup']);
   $router->put('/statistic', ['uses' => 'StatisticController@put']);
   $router->delete('/statistic', ['uses' => 'StatisticController@delete']);
   $router->post('/statistic/masive_load', ['uses' => 'StatisticController@masiveLoad']);

   //CRUD MonitoringTool
   $router->post('/monitoringtool', ['uses' => 'MonitoringToolController@post']);
   $router->get('/monitoringtool', ['uses' => 'MonitoringToolController@get']);
   $router->get('/monitoringtool/paginate', ['uses' => 'MonitoringToolController@paginate']);
   $router->get('/monitoringtool/backup', ['uses' => 'MonitoringToolController@backup']);
   $router->put('/monitoringtool', ['uses' => 'MonitoringToolController@put']);
   $router->delete('/monitoringtool', ['uses' => 'MonitoringToolController@delete']);
   $router->post('/monitoringtool/masive_load', ['uses' => 'MonitoringToolController@masiveLoad']);

   //CRUD TargetType
   $router->post('/targettype', ['uses' => 'TargetTypeController@post']);
   $router->get('/targettype', ['uses' => 'TargetTypeController@get']);
   $router->get('/targettype/paginate', ['uses' => 'TargetTypeController@paginate']);
   $router->get('/targettype/backup', ['uses' => 'TargetTypeController@backup']);
   $router->put('/targettype', ['uses' => 'TargetTypeController@put']);
   $router->delete('/targettype', ['uses' => 'TargetTypeController@delete']);
   $router->post('/targettype/masive_load', ['uses' => 'TargetTypeController@masiveLoad']);
});
