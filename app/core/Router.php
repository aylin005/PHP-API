<?php

namespace App\Core;

class Router {
    private $routes = [];

    const BASE_PATH = '/PHP-API';

    // ahora acepta string (path a archivo) o callable
    public function add($route, $handler) {
        $this->routes[$route] = ['handler' => $handler];
    }

    public function addMiddleware($route, $middleware) {
        $this->routes[$route]['middleware'] = $middleware;
    }

    public function dispatch($uri) {
        $path = parse_url($uri, PHP_URL_PATH);

        $routePath = str_replace(static::BASE_PATH, '', $path, $count);
        if ($count === 0) {
            $routePath = $path;
        }
        if (empty($routePath)) {
            $routePath = '/';
        }

        foreach ($this->routes as $key => $route) {
            $routePattern = preg_replace('/:\w+/', '([^/]+)', trim($key, '/'));

            if (preg_match('#^' . $routePattern . '$#', trim($routePath, '/'), $matches)) {
                $middleware = $route['middleware'] ?? null;
                if ($middleware) {
                    require $middleware;
                }

                $handler = $route['handler'];

                // quitar el primer elemento de $matches (texto completo)
                array_shift($matches);

                if (is_callable($handler)) {
                    // pasar METHOD y todos los parámetros capturados
                    call_user_func_array($handler, array_merge([$_SERVER['REQUEST_METHOD']], $matches));
                    return;
                }

                // compatibilidad: si se pasó ruta a archivo
                if (is_string($handler) && file_exists($handler)) {
                    require $handler;
                    return;
                }

                // si se pasó "ControllerClass@method" (opcional futuro)
                if (is_string($handler) && strpos($handler, '@') !== false) {
                    list($class, $method) = explode('@', $handler, 2);
                    if (class_exists($class)) {
                        $instance = new $class();
                        $instance->{$method}($_SERVER['REQUEST_METHOD'], ...$matches);
                        return;
                    }
                }

                // no se pudo resolver handler
                $this->abort(500);
            }
        }

        $this->abort(404);
    }

    public function abort($code = 404) {
        http_response_code($code);
        echo "Error $code: Page not found.";
        exit();
    }
}