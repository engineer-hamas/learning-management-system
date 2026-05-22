# Programmatic Routing Subskill

This subskill outlines the design and syntax of our programmatic OOP routing engine. All incoming web requests are funneled through `public/index.php` (the Front Controller), which parses the URL and invokes the registered controller action.

## Front Controller (`public/index.php`)

The entrypoint initializes global configurations, registers the autoloader, instantiates the programmatic router, registers routes, and dispatches.

```php
<?php
// Initialize configuration & session
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

// Register autoloader
require_once __DIR__ . '/../src/Core/Autoloader.php';
\App\Core\Autoloader::register();

use App\Core\Router;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;

$router = new Router();

// Define Routes
$router->get('/', function($req, $res) {
    if (isset($_SESSION['user_id'])) {
        $res->redirect('/dashboard');
    } else {
        $res->redirect('/login');
    }
});

$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'handleLogin']);

$router->get('/register', [AuthController::class, 'showRegister']);
$router->post('/register', [AuthController::class, 'handleRegister']);

$router->get('/logout', [AuthController::class, 'handleLogout']);
$router->get('/dashboard', [DashboardController::class, 'index']);

// Dispatch Request
$router->dispatch(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH),
    $_SERVER['REQUEST_METHOD']
);
```

## Programmatic Router Class (`src/Core/Router.php`)

The Router maintains GET and POST route registries and routes incoming methods + paths to target handlers (callbacks or controller classes).

```php
<?php
namespace App\Core;

class Router {
    private array $routes = [];

    /**
     * Map a GET request route
     */
    public function get(string $path, $handler): void {
        $this->routes['GET'][$path] = $handler;
    }

    /**
     * Map a POST request route
     */
    public function post(string $path, $handler): void {
        $this->routes['POST'][$path] = $handler;
    }

    /**
     * Dispatch the current request to a handler
     */
    public function dispatch(string $uri, string $method): void {
        $handler = $this->routes[$method][$uri] ?? null;

        if (!$handler) {
            $this->sendNotFound();
            return;
        }

        $request = new Request();
        $response = new Response();

        if (is_callable($handler)) {
            $handler($request, $response);
            return;
        }

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
```

## Adding a New Route
To register a new URL endpoint:
1. Locate the route definition block in `public/index.php`.
2. Add a new registration line:
   ```php
   $router->get('/my-new-path', [MyNewController::class, 'myAction']);
   ```
3. Create the corresponding controller class and method.
