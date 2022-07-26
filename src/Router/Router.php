<?php

namespace Src\Router;

use AltoRouter;

/**
 * @package Src\Router
 */
class Router extends AltoRouter
{
    /**
     * @param string $target
     * @param array $params
     * @return void
     */
    public function routerRequest(string $target, array $params)
    {
        if (stripos($target, '#') !== false) {
            list($controller, $method) = explode('#', $target, 2);
            $cname = "\App\Controllers\\" . $controller;
            $controllerName = new $cname;
            if ($params) {
                return call_user_func_array(array($controllerName, $method), array($params['id']));
            }
            return call_user_func(array($controllerName, $method));
        }
        // Redirect to 404
    }
}
