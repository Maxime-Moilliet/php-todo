<?php

require_once 'vendor/autoload.php';

use Src\Router\Router;

$router = new Router();

$router->map('GET', '/', 'TodoController#index');
$router->map('POST', '/store', 'TodoController#store');
$router->map('POST', '/update/[i:id]', 'TodoController#update');
$router->map('POST', '/delete/[i:id]', 'TodoController#delete');

$match = $router->match();

$router->routerRequest($match['target'], $match['params']);