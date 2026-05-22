<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Request;
use App\Core\Response;

/**
 * Class AuthController
 * 
 * Handles user registration, login verification, and session termination.
 */
class AuthController extends Controller {
    private $db;

    public function __construct() {
        // Load the singleton database connection
        $this->db = Database::getConnection();
    }

    /**
     * Show registration page
     * 
     * @param Request $request
     * @param Response $response
     */
    public function showRegister(Request $request, Response $response): void {
        // Redirect active sessions to dashboard
        if (isset($_SESSION['user_id'])) {
            $this->redirect('/dashboard');
        }

        $error = $_SESSION['register_error'] ?? '';
        $success = $_SESSION['register_success'] ?? '';
        unset($_SESSION['register_error'], $_SESSION['register_success']);

        $this->render('register', [
            'error' => $error,
            'success' => $success
        ]);
    }

    /**
     * Handle registration submission
     * 
     * @param Request $request
     * @param Response $response
     */
    public function handleRegister(Request $request, Response $response): void {
        if (!$request->isPost()) {
            $this->redirect('/register');
        }

        // Clean and retrieve values
        $name = $request->cleanPost('name');
        $email = $request->cleanPost('email');
        $password = $request->post('password');
        $role = $request->cleanPost('role', 'student');

        // Validation constraints check
        if (empty($name) || empty($email) || empty($password) || empty($role)) {
            $_SESSION['register_error'] = "All fields are required.";
            $this->redirect('/register');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['register_error'] = "Invalid email format.";
            $this->redirect('/register');
        }

        if (!in_array($role, ['student', 'teacher'])) {
            $_SESSION['register_error'] = "Invalid role selected.";
            $this->redirect('/register');
        }

        // Check if email already exists via prepared query
        $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");
        if (!$stmt) {
            $_SESSION['register_error'] = "Database system error. Please try again.";
            $this->redirect('/register');
        }
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $_SESSION['register_error'] = "Email is already registered.";
            $stmt->close();
            $this->redirect('/register');
        }
        $stmt->close();

        // Securely hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert new user record
        $stmt = $this->db->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        if (!$stmt) {
            $_SESSION['register_error'] = "Database insertion failed. Please try again.";
            $this->redirect('/register');
        }
        $stmt->bind_param("ssss", $name, $email, $hashedPassword, $role);

        if ($stmt->execute()) {
            $_SESSION['register_success'] = "Registration successful! You can now log in.";
            $stmt->close();
            $this->redirect('/login');
        } else {
            $_SESSION['register_error'] = "Registration failed. Please try again.";
            $stmt->close();
            $this->redirect('/register');
        }
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
        ]);
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

        // Fetch user from DB using prepared query
        $stmt = $this->db->prepare("SELECT id, name, email, password, role FROM users WHERE email = ?");
        if (!$stmt) {
            $_SESSION['login_error'] = "Database lookup failed. Please try again.";
            $this->redirect('/login');
        }
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Verify password hash
            if (password_verify($password, $user['password'])) {
                // Regenerate session ID for security (prevents session fixation)
                session_regenerate_id(true);

                // Set session data payloads
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['user_email'] = $user['email'];

                $stmt->close();
                $this->redirect('/dashboard');
            }
        }

        // Invalid credentials
        $_SESSION['login_error'] = "Invalid email or password.";
        $stmt->close();
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
