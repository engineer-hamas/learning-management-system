<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Models\User;

/**
 * Class AuthController
 * 
 * Handles user registration, login verification, and session termination.
 */
class AuthController extends Controller {
    private User $userModel;

    public function __construct() {
        $this->userModel = new User();
    }


    /**
     * Show login page
     * 
     * @param Request $request
     * @param Response $response
     */
    public function showLogin(Request $request, Response $response): void {
        // Redirect active sessions to dashboard
        if (isset($_SESSION['user_id'])) {
            $this->redirect('/dashboard');
        }

        $error = $_SESSION['login_error'] ?? '';
        $success = $_SESSION['register_success'] ?? '';
        unset($_SESSION['login_error'], $_SESSION['register_success']);

        $this->render('login', [
            'error' => $error,
            'success' => $success
        ], 'auth');
    }

    /**
     * Handle login submission
     * 
     * @param Request $request
     * @param Response $response
     */
    public function handleLogin(Request $request, Response $response): void {
        if (!$request->isPost()) {
            $this->redirect('/login');
        }

        $email = $request->cleanPost('email');
        $password = $request->post('password');

        if (empty($email) || empty($password)) {
            $_SESSION['login_error'] = "Email and password are required.";
            $this->redirect('/login');
        }

        $user = $this->userModel->findByEmail($email);

        if ($user && password_verify($password, $user['password'])) {
            // Regenerate session ID to prevent session fixation attacks
            session_regenerate_id(true);

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_email'] = $user['email'];

            $this->redirect('/dashboard');
        } else {
            $_SESSION['login_error'] = "Invalid email or password.";
            $this->redirect('/login');
        }
        $this->redirect('/login');
    }

    /**
     * Handle logout
     * 
     * @param Request $request
     * @param Response $response
     */
    public function handleLogout(Request $request, Response $response): void {
        $_SESSION = [];
        
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        session_destroy();
        $this->redirect('/login');
    }
}
