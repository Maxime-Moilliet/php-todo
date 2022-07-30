<?php

namespace Src\Router;

use Exception;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\HttpFoundation\RedirectResponse;

class RegisterController
{
    public function __construct(array $controllers)
    {
        foreach ($controllers as $controller) {
            try {
                $this->register($controller);
            } catch (ReflectionException|Exception $e) {
                return $e->getMessage();
            }
        }
    }

    /**
     * @param string $controller
     * @return RedirectResponse|void
     * @throws ReflectionException
     * @throws Exception
     */
    public function register(string $controller)
    {
        $class = new ReflectionClass($controller);
        $routeAttributes = $class->getAttributes();
        $prefix = '';
        if (!empty($routeAttributes)) {
            $prefix = $routeAttributes[0]->newInstance()->getPath();
        }
        $router = new Router();
        foreach ($class->getMethods() as $method) {
            $routeAttributes = $method->getAttributes(Route::class);
            if (empty($routeAttributes)) {
                continue;
            }
            foreach ($routeAttributes as $routeAttribute) {
                /** @var Route $route */
                $route = $routeAttribute->newInstance();
                $router->map($route->getMethod(), $prefix . $route->getPath(), $method->class . '#' . $method->name);
            }
        }
        $match = $router->match();
        if (!$match) {
            return (new RedirectResponse('/404'))->send();
        }
        $router->routerRequest($match['target'], $match['params']);
    }
}
