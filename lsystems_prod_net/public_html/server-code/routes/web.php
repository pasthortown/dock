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

   $router->post('/buildAll', ['uses' => 'GeneratorController@buildAll']);
   $router->post('/saveMigrationOfMany2Many', ['uses' => 'GeneratorController@saveMigrationOfMany2Many']);
   $router->get('/getFromOutput', ['uses' => 'GeneratorController@getFromOutput']);
});

$router->group(['middleware' => ['auth']], function () use ($router) {
   $router->post('/user/password_change', ['uses' => 'AuthController@passwordChange']);


   //LSCodeGenerator

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

   //CRUD Project
   $router->post('/project', ['uses' => 'ProjectController@post']);
   $router->get('/project', ['uses' => 'ProjectController@get']);
   $router->get('/project/paginate', ['uses' => 'ProjectController@paginate']);
   $router->get('/project/backup', ['uses' => 'ProjectController@backup']);
   $router->put('/project', ['uses' => 'ProjectController@put']);
   $router->delete('/project', ['uses' => 'ProjectController@delete']);
   $router->post('/project/masive_load', ['uses' => 'ProjectController@masiveLoad']);
   $router->post('/project/my_projects', ['uses' => 'ProjectController@my_projects']);

   //CRUD ProjectAttachment
   $router->post('/projectattachment', ['uses' => 'ProjectAttachmentController@post']);
   $router->get('/projectattachment', ['uses' => 'ProjectAttachmentController@get']);
   $router->get('/projectattachment/paginate', ['uses' => 'ProjectAttachmentController@paginate']);
   $router->get('/projectattachment/backup', ['uses' => 'ProjectAttachmentController@backup']);
   $router->put('/projectattachment', ['uses' => 'ProjectAttachmentController@put']);
   $router->delete('/projectattachment', ['uses' => 'ProjectAttachmentController@delete']);
   $router->post('/projectattachment/masive_load', ['uses' => 'ProjectAttachmentController@masiveLoad']);

   //CRUD ProjectType
   $router->post('/projecttype', ['uses' => 'ProjectTypeController@post']);
   $router->get('/projecttype', ['uses' => 'ProjectTypeController@get']);
   $router->get('/projecttype/paginate', ['uses' => 'ProjectTypeController@paginate']);
   $router->get('/projecttype/backup', ['uses' => 'ProjectTypeController@backup']);
   $router->put('/projecttype', ['uses' => 'ProjectTypeController@put']);
   $router->delete('/projecttype', ['uses' => 'ProjectTypeController@delete']);
   $router->post('/projecttype/masive_load', ['uses' => 'ProjectTypeController@masiveLoad']);
});
