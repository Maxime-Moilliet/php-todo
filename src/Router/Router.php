<?php

namespace Src\Router;

use AltoRouter;

class Router extends AltoRouter
{
    public function routerRequest(string $target, array $params)
    {
        list($controller, $method) = explode('#', $target, 2);
        $cname = $controller;
        $controllerName = new $cname;
        if ($params) {
            return call_user_func_array(array($controllerName, $method), array($params['id']));
        }
        return call_user_func(array($controllerName, $method));
    }
}
