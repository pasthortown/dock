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


   //LSTracking

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

   //CRUD MobileType
   $router->post('/mobiletype', ['uses' => 'MobileTypeController@post']);
   $router->get('/mobiletype', ['uses' => 'MobileTypeController@get']);
   $router->get('/mobiletype/paginate', ['uses' => 'MobileTypeController@paginate']);
   $router->get('/mobiletype/backup', ['uses' => 'MobileTypeController@backup']);
   $router->put('/mobiletype', ['uses' => 'MobileTypeController@put']);
   $router->delete('/mobiletype', ['uses' => 'MobileTypeController@delete']);
   $router->post('/mobiletype/masive_load', ['uses' => 'MobileTypeController@masiveLoad']);

   //CRUD MobileAttachment
   $router->post('/mobileattachment', ['uses' => 'MobileAttachmentController@post']);
   $router->get('/mobileattachment', ['uses' => 'MobileAttachmentController@get']);
   $router->get('/mobileattachment/paginate', ['uses' => 'MobileAttachmentController@paginate']);
   $router->get('/mobileattachment/backup', ['uses' => 'MobileAttachmentController@backup']);
   $router->put('/mobileattachment', ['uses' => 'MobileAttachmentController@put']);
   $router->delete('/mobileattachment', ['uses' => 'MobileAttachmentController@delete']);
   $router->post('/mobileattachment/masive_load', ['uses' => 'MobileAttachmentController@masiveLoad']);

   //CRUD Mobile
   $router->post('/mobile', ['uses' => 'MobileController@post']);
   $router->get('/mobile', ['uses' => 'MobileController@get']);
   $router->get('/mobile/paginate', ['uses' => 'MobileController@paginate']);
   $router->get('/mobile/backup', ['uses' => 'MobileController@backup']);
   $router->put('/mobile', ['uses' => 'MobileController@put']);
   $router->delete('/mobile', ['uses' => 'MobileController@delete']);
   $router->post('/mobile/masive_load', ['uses' => 'MobileController@masiveLoad']);

   //CRUD MobilePosition
   $router->post('/mobileposition', ['uses' => 'MobilePositionController@post']);
   $router->get('/mobileposition', ['uses' => 'MobilePositionController@get']);
   $router->get('/mobileposition/paginate', ['uses' => 'MobilePositionController@paginate']);
   $router->get('/mobileposition/backup', ['uses' => 'MobilePositionController@backup']);
   $router->put('/mobileposition', ['uses' => 'MobilePositionController@put']);
   $router->delete('/mobileposition', ['uses' => 'MobilePositionController@delete']);
   $router->post('/mobileposition/masive_load', ['uses' => 'MobilePositionController@masiveLoad']);
});
