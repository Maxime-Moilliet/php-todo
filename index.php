<?php

use App\Controllers\TodoController;
use Src\Router\RegisterController;

require_once 'vendor/autoload.php';

$registerController = new RegisterController([
    TodoController::class
]);
