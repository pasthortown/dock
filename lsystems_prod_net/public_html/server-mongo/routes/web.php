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


   //videoconference

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

   //CRUD ResourceType
   $router->post('/resourcetype', ['uses' => 'ResourceTypeController@post']);
   $router->get('/resourcetype', ['uses' => 'ResourceTypeController@get']);
   $router->get('/resourcetype/paginate', ['uses' => 'ResourceTypeController@paginate']);
   $router->get('/resourcetype/backup', ['uses' => 'ResourceTypeController@backup']);
   $router->put('/resourcetype', ['uses' => 'ResourceTypeController@put']);
   $router->delete('/resourcetype', ['uses' => 'ResourceTypeController@delete']);
   $router->post('/resourcetype/masive_load', ['uses' => 'ResourceTypeController@masiveLoad']);

   //CRUD ScheduleType
   $router->post('/scheduletype', ['uses' => 'ScheduleTypeController@post']);
   $router->get('/scheduletype', ['uses' => 'ScheduleTypeController@get']);
   $router->get('/scheduletype/paginate', ['uses' => 'ScheduleTypeController@paginate']);
   $router->get('/scheduletype/backup', ['uses' => 'ScheduleTypeController@backup']);
   $router->put('/scheduletype', ['uses' => 'ScheduleTypeController@put']);
   $router->delete('/scheduletype', ['uses' => 'ScheduleTypeController@delete']);
   $router->post('/scheduletype/masive_load', ['uses' => 'ScheduleTypeController@masiveLoad']);

   //CRUD GuestType
   $router->post('/guesttype', ['uses' => 'GuestTypeController@post']);
   $router->get('/guesttype', ['uses' => 'GuestTypeController@get']);
   $router->get('/guesttype/paginate', ['uses' => 'GuestTypeController@paginate']);
   $router->get('/guesttype/backup', ['uses' => 'GuestTypeController@backup']);
   $router->put('/guesttype', ['uses' => 'GuestTypeController@put']);
   $router->delete('/guesttype', ['uses' => 'GuestTypeController@delete']);
   $router->post('/guesttype/masive_load', ['uses' => 'GuestTypeController@masiveLoad']);

   //CRUD Resource
   $router->post('/resource', ['uses' => 'ResourceController@post']);
   $router->get('/resource', ['uses' => 'ResourceController@get']);
   $router->get('/resource/paginate', ['uses' => 'ResourceController@paginate']);
   $router->get('/resource/backup', ['uses' => 'ResourceController@backup']);
   $router->put('/resource', ['uses' => 'ResourceController@put']);
   $router->delete('/resource', ['uses' => 'ResourceController@delete']);
   $router->post('/resource/masive_load', ['uses' => 'ResourceController@masiveLoad']);

   //CRUD Schedule
   $router->post('/schedule', ['uses' => 'ScheduleController@post']);
   $router->get('/schedule', ['uses' => 'ScheduleController@get']);
   $router->get('/schedule/paginate', ['uses' => 'ScheduleController@paginate']);
   $router->get('/schedule/backup', ['uses' => 'ScheduleController@backup']);
   $router->put('/schedule', ['uses' => 'ScheduleController@put']);
   $router->delete('/schedule', ['uses' => 'ScheduleController@delete']);
   $router->post('/schedule/masive_load', ['uses' => 'ScheduleController@masiveLoad']);

   //CRUD Guest
   $router->post('/guest', ['uses' => 'GuestController@post']);
   $router->get('/guest', ['uses' => 'GuestController@get']);
   $router->get('/guest/paginate', ['uses' => 'GuestController@paginate']);
   $router->get('/guest/backup', ['uses' => 'GuestController@backup']);
   $router->put('/guest', ['uses' => 'GuestController@put']);
   $router->delete('/guest', ['uses' => 'GuestController@delete']);
   $router->post('/guest/masive_load', ['uses' => 'GuestController@masiveLoad']);

   //CRUD Responsable
   $router->post('/responsable', ['uses' => 'ResponsableController@post']);
   $router->get('/responsable', ['uses' => 'ResponsableController@get']);
   $router->get('/responsable/paginate', ['uses' => 'ResponsableController@paginate']);
   $router->get('/responsable/backup', ['uses' => 'ResponsableController@backup']);
   $router->put('/responsable', ['uses' => 'ResponsableController@put']);
   $router->delete('/responsable', ['uses' => 'ResponsableController@delete']);
   $router->post('/responsable/masive_load', ['uses' => 'ResponsableController@masiveLoad']);

   //CRUD ScheduleResourceAssigment
   $router->post('/scheduleresourceassigment', ['uses' => 'ScheduleResourceAssigmentController@post']);
   $router->get('/scheduleresourceassigment', ['uses' => 'ScheduleResourceAssigmentController@get']);
   $router->get('/scheduleresourceassigment/paginate', ['uses' => 'ScheduleResourceAssigmentController@paginate']);
   $router->get('/scheduleresourceassigment/backup', ['uses' => 'ScheduleResourceAssigmentController@backup']);
   $router->put('/scheduleresourceassigment', ['uses' => 'ScheduleResourceAssigmentController@put']);
   $router->delete('/scheduleresourceassigment', ['uses' => 'ScheduleResourceAssigmentController@delete']);
   $router->post('/scheduleresourceassigment/masive_load', ['uses' => 'ScheduleResourceAssigmentController@masiveLoad']);

   //CRUD ScheduleResourceAssistant
   $router->post('/scheduleresourceassistant', ['uses' => 'ScheduleResourceAssistantController@post']);
   $router->get('/scheduleresourceassistant', ['uses' => 'ScheduleResourceAssistantController@get']);
   $router->get('/scheduleresourceassistant/paginate', ['uses' => 'ScheduleResourceAssistantController@paginate']);
   $router->get('/scheduleresourceassistant/backup', ['uses' => 'ScheduleResourceAssistantController@backup']);
   $router->put('/scheduleresourceassistant', ['uses' => 'ScheduleResourceAssistantController@put']);
   $router->delete('/scheduleresourceassistant', ['uses' => 'ScheduleResourceAssistantController@delete']);
   $router->post('/scheduleresourceassistant/masive_load', ['uses' => 'ScheduleResourceAssistantController@masiveLoad']);

   //CRUD ScheduleResponsableAssigment
   $router->post('/scheduleresponsableassigment', ['uses' => 'ScheduleResponsableAssigmentController@post']);
   $router->get('/scheduleresponsableassigment', ['uses' => 'ScheduleResponsableAssigmentController@get']);
   $router->get('/scheduleresponsableassigment/paginate', ['uses' => 'ScheduleResponsableAssigmentController@paginate']);
   $router->get('/scheduleresponsableassigment/backup', ['uses' => 'ScheduleResponsableAssigmentController@backup']);
   $router->put('/scheduleresponsableassigment', ['uses' => 'ScheduleResponsableAssigmentController@put']);
   $router->delete('/scheduleresponsableassigment', ['uses' => 'ScheduleResponsableAssigmentController@delete']);
   $router->post('/scheduleresponsableassigment/masive_load', ['uses' => 'ScheduleResponsableAssigmentController@masiveLoad']);

   //CRUD Role
   $router->post('/role', ['uses' => 'RoleController@post']);
   $router->get('/role', ['uses' => 'RoleController@get']);
   $router->get('/role/paginate', ['uses' => 'RoleController@paginate']);
   $router->get('/role/backup', ['uses' => 'RoleController@backup']);
   $router->put('/role', ['uses' => 'RoleController@put']);
   $router->delete('/role', ['uses' => 'RoleController@delete']);
   $router->post('/role/masive_load', ['uses' => 'RoleController@masiveLoad']);
});
