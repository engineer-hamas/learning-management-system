<?php
// Main Application Router (Front Controller)

// Serve static files when running under the built-in PHP web server
if (php_sapi_name() === 'cli-server') {
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    if (is_file(__DIR__ . $path)) {
        return false;
    }
}

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
use App\Controllers\ProfileController;
use App\Controllers\EnrollmentController;
use App\Controllers\CourseController;

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

// Enrollments Routes
$router->get('/enrollments', [EnrollmentController::class, 'index']);
$router->post('/enrollments/add', [EnrollmentController::class, 'enrollStudent']);
$router->post('/enrollments/remove', [EnrollmentController::class, 'removeStudent']);

// Course Content Routes
$router->get('/course', [CourseController::class, 'show']);
$router->get('/course/attendance', [CourseController::class, 'showAttendance']);
$router->get('/course/attendance/take', [CourseController::class, 'takeAttendance']);
$router->post('/course/attendance/mark', [CourseController::class, 'markAttendance']);
$router->post('/course/material', [CourseController::class, 'addMaterial']);
$router->post('/course/assignment', [CourseController::class, 'addAssignment']);
$router->post('/course/exam', [CourseController::class, 'addExam']);
$router->post('/course/quiz', [CourseController::class, 'addQuiz']);

$router->post('/course/material/delete', [CourseController::class, 'deleteMaterial']);
$router->post('/course/assignment/delete', [CourseController::class, 'deleteAssignment']);
$router->post('/course/exam/delete', [CourseController::class, 'deleteExam']);
$router->post('/course/quiz/delete', [CourseController::class, 'deleteQuiz']);

// Settings Routes
$router->get('/settings', [ProfileController::class, 'showSettings']);
$router->post('/settings/profile', [ProfileController::class, 'handleProfileUpdate']);
$router->post('/settings/password', [ProfileController::class, 'handlePasswordUpdate']);
$router->post('/settings/notifications', [ProfileController::class, 'handleNotificationsUpdate']);

// 5. Dispatch current route
$router->dispatch(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH),
    $_SERVER['REQUEST_METHOD']
);
