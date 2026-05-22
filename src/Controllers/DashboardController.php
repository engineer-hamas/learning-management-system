<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;

/**
 * Class DashboardController
 * 
 * Handles rendering the main workspace dashboard view for students/teachers.
 */
class DashboardController extends Controller {
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

        // Fetch fresh avatar_path from DB to keep it always accurate
        $db = \App\Core\Database::getConnection();
        $stmt = $db->prepare("SELECT avatar_path FROM users WHERE id = ?");
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $userAvatar = null;
        if ($result && $result->num_rows === 1) {
            $user = $result->fetch_assoc();
            $userAvatar = $user['avatar_path'];
        }
        $stmt->close();

        // Render using modern base view templates and extract data params
        $this->render('dashboard', [
            'userRole' => $_SESSION['user_role'],
            'userName' => $_SESSION['user_name'],
            'userEmail' => $_SESSION['user_email'],
            'userAvatar' => $userAvatar
        ]);
    }
}
