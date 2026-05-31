<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;

use App\Models\User;

/**
 * Class DashboardController
 * 
 * Handles rendering the main workspace dashboard view for students/teachers.
 */
class DashboardController extends Controller {
    private User $userModel;

    public function __construct() {
        $this->userModel = new User();
    }
    /**
     * Show primary dashboard interface
     * 
     * @param Request $request
     * @param Response $response
     */
    public function index(Request $request, Response $response): void {
        // Authenticate user session presence
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
        }

        // Fetch fresh avatar_path from DB
        $user = $this->userModel->findById($_SESSION['user_id']);
        $userAvatar = $user['avatar_path'] ?? null;

        // Render using modern base view templates and extract data params
        $this->render('dashboard', [
            'userRole' => $_SESSION['user_role'],
            'userName' => $_SESSION['user_name'],
            'userEmail' => $_SESSION['user_email'],
            'userAvatar' => $userAvatar
        ]);
    }
}
