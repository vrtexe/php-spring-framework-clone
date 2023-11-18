<?php

namespace Vangel\Project\Core;

use Exception;

class Router
{
    public array $routes = [];

    public function registerController(object $controller): self
    {
        $controllerRoutes = ControllerFactory::register($controller);

        foreach ($controllerRoutes as $controllerRoute) {
            $this->routes[$controllerRoute->method->value] = [
                ...($this->routes[$controllerRoute->method->value] ?? []),
                $controllerRoute->path => $controllerRoute->action
            ];
        }

        return $this;
    }


    /**
     * @throws Exception
     */
    public function resolve()
    {
        $requestUri = $_SERVER['REQUEST_URI'];
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $route = explode('?', $requestUri)[0];
        $action = $this->routes[$requestMethod][$route] ?? null;

        if (!$route) {
            throw new Exception("404 Not Found");
        }

        return call_user_func($action, []);
    }
}
