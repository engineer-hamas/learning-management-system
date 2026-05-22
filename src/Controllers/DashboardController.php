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

        // Render using modern base view templates and extract data params
        $this->render('dashboard', [
            'userRole' => $_SESSION['user_role'],
            'userName' => $_SESSION['user_name'],
            'userEmail' => $_SESSION['user_email']
        ]);
    }
}
