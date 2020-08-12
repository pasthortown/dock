<?php
include_once('./args.php');
include_once('./generador.php');

header('Content-type:application/json;charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');

function route(){
    $uri = $_SERVER['REQUEST_URI'];
    $route_path = explode('?',$uri)[0];
    $route_parts = explode('/',$route_path);
    $action = strtolower($route_parts[count($route_parts)-1]);
    $apiGenerator = new ApiGenerator('input/Models');
    return json_encode($apiGenerator->$action(getArgs()));
}

echo route();