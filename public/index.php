<?php
// Main Application Router (Front Controller)

// 1. Load application configuration (includes session startup)
require_once __DIR__ . '/../config/config.php';

// 2. Load globally available helper functions
require_once __DIR__ . '/../includes/functions.php';

// 3. Register custom PSR-4 autoloader
require_once __DIR__ . '/../src/Core/Autoloader.php';
\App\Core\Autoloader::register();

use App\Core\Router;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;

// 4. Initialize Programmatic OOP Router
$router = new Router();

// Define Application Routes

// Redirect base URL depending on active session state
$router->get('/', function($request, $response) {
    if (isset($_SESSION['user_id'])) {
        $response->redirect('/dashboard');
    } else {
        $response->redirect('/login');
    }
});

// Authentication Routes
$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'handleLogin']);

$router->get('/register', [AuthController::class, 'showRegister']);
$router->post('/register', [AuthController::class, 'handleRegister']);

$router->get('/logout', [AuthController::class, 'handleLogout']);

// Dashboard Route
$router->get('/dashboard', [DashboardController::class, 'index']);

// 5. Dispatch current route
$router->dispatch(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH),
    $_SERVER['REQUEST_METHOD']
);
