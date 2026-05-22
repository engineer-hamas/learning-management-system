<?php
namespace App\Core;

/**
 * Class Router
 * 
 * Programmatic routing engine that dispatches requests to controller methods.
 */
class Router {
    private array $routes = [];

    /**
     * Map a GET request route
     * 
     * @param string $path URL path pattern
     * @param mixed $handler Callback function or [Controller::class, 'method']
     */
    public function get(string $path, $handler): void {
        $this->routes['GET'][$path] = $handler;
    }

    /**
     * Map a POST request route
     * 
     * @param string $path URL path pattern
     * @param mixed $handler Callback function or [Controller::class, 'method']
     */
    public function post(string $path, $handler): void {
        $this->routes['POST'][$path] = $handler;
    }

    /**
     * Dispatch the current request to a registered handler
     * 
     * @param string $uri The requested URL path
     * @param string $method The request HTTP method
     */
    public function dispatch(string $uri, string $method): void {
        $handler = $this->routes[$method][$uri] ?? null;

        if (!$handler) {
            $this->sendNotFound();
            return;
        }

        $request = new Request();
        $response = new Response();

        // 1. Process Closure / Anonymous Function handlers
        if (is_callable($handler)) {
            $handler($request, $response);
            return;
        }

        // 2. Process Controller actions
        if (is_array($handler)) {
            [$controllerClass, $methodName] = $handler;
            if (class_exists($controllerClass)) {
                $controller = new $controllerClass();
                if (method_exists($controller, $methodName)) {
                    $controller->$methodName($request, $response);
                    return;
                }
            }
        }

        $this->sendNotFound();
    }

    /**
     * Fallback for 404 routes
     */
    private function sendNotFound(): void {
        http_response_code(404);
        (new Response())->render('404');
    }
}
Class_Alias('App\\Core\\Router', 'Router');
