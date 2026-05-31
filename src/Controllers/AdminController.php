<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Models\User;
use App\Models\Course;

/**
 * Class AdminController
 * 
 * Handles rendering the admin dashboard and processing admin-specific actions.
 */
class AdminController extends Controller {
    private User $userModel;
    private Course $courseModel;

    public function __construct() {
        $this->userModel = new User();
        $this->courseModel = new Course();
    }

    private function requireAdmin(): void {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            $this->redirect('/login');
        }
    }

    /**
     * Show admin dashboard
     * 
     * @param Request $request
     * @param Response $response
     */
    public function dashboard(Request $request, Response $response): void {
        $this->requireAdmin();

        $teachers = $this->userModel->getByRole('teacher');
        $students = $this->userModel->getByRole('student');
        $courses = $this->courseModel->getAll();

        $this->render('admin/dashboard', [
            'userRole' => $_SESSION['user_role'],
            'userName' => $_SESSION['user_name'],
            'teachersCount' => count($teachers),
            'studentsCount' => count($students),
            'coursesCount' => count($courses)
        ]);
    }

    /**
     * Show create user form
     */
    public function showCreateUser(Request $request, Response $response): void {
        $this->requireAdmin();

        $error = $_SESSION['create_user_error'] ?? '';
        $success = $_SESSION['create_user_success'] ?? '';
        unset($_SESSION['create_user_error'], $_SESSION['create_user_success']);

        $this->render('admin/create-user', [
            'userRole' => $_SESSION['user_role'],
            'error' => $error,
            'success' => $success
        ]);
    }

    /**
     * Handle create user submission
     */
    public function handleCreateUser(Request $request, Response $response): void {
        $this->requireAdmin();

        if (!$request->isPost()) {
            $this->redirect('/admin/users/create');
        }

        $name = $request->cleanPost('name');
        $email = $request->cleanPost('email');
        $password = $request->post('password');
        $role = $request->cleanPost('role');

        if (empty($name) || empty($email) || empty($password) || empty($role)) {
            $_SESSION['create_user_error'] = "All fields are required.";
            $this->redirect('/admin/users/create');
        }

        if (!in_array($role, ['teacher', 'student'])) {
            $_SESSION['create_user_error'] = "Invalid role selected.";
            $this->redirect('/admin/users/create');
        }

        if ($this->userModel->emailExists($email)) {
            $_SESSION['create_user_error'] = "Email is already registered.";
            $this->redirect('/admin/users/create');
        }

        try {
            $this->userModel->create($name, $email, $password, $role);
            $_SESSION['create_user_success'] = ucfirst($role) . " account created successfully.";
            $this->redirect('/admin/users/create');
        } catch (\Exception $e) {
            $_SESSION['create_user_error'] = "Failed to create user: " . $e->getMessage();
            $this->redirect('/admin/users/create');
        }
    }

    /**
     * Show create course form
     */
    public function showCreateCourse(Request $request, Response $response): void {
        $this->requireAdmin();

        $error = $_SESSION['create_course_error'] ?? '';
        $success = $_SESSION['create_course_success'] ?? '';
        unset($_SESSION['create_course_error'], $_SESSION['create_course_success']);

        // Assuming departments exist in db, but to keep it simple we can fetch them or hardcode a dropdown if we had a Department model
        // We'll just manually use a DB connection to get departments for the dropdown
        $db = \App\Core\Database::getConnection();
        $res = $db->query("SELECT id, name FROM departments");
        $departments = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];

        $this->render('admin/create-course', [
            'userRole' => $_SESSION['user_role'],
            'departments' => $departments,
            'error' => $error,
            'success' => $success
        ]);
    }

    /**
     * Handle create course submission
     */
    public function handleCreateCourse(Request $request, Response $response): void {
        $this->requireAdmin();

        if (!$request->isPost()) {
            $this->redirect('/admin/courses/create');
        }

        $departmentId = (int)$request->post('department_id');
        $name = $request->cleanPost('name');
        $code = $request->cleanPost('code');
        $description = $request->cleanPost('description');
        $credits = (int)$request->post('credits');

        if (empty($name) || empty($code) || empty($departmentId)) {
            $_SESSION['create_course_error'] = "Department, Name, and Code are required.";
            $this->redirect('/admin/courses/create');
        }

        try {
            $this->courseModel->create($departmentId, $name, $code, $description, $credits ?: 3);
            $_SESSION['create_course_success'] = "Course created successfully.";
            $this->redirect('/admin/courses/create');
        } catch (\Exception $e) {
            $_SESSION['create_course_error'] = "Failed to create course: " . $e->getMessage();
            $this->redirect('/admin/courses/create');
        }
    }
}
