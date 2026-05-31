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

    /**
     * List all users
     */
    public function listUsers(Request $request, Response $response): void {
        $this->requireAdmin();

        $users = $this->userModel->getAllNonAdmins();
        
        $error = $_SESSION['manage_user_error'] ?? '';
        $success = $_SESSION['manage_user_success'] ?? '';
        unset($_SESSION['manage_user_error'], $_SESSION['manage_user_success']);

        $this->render('admin/users-list', [
            'userRole' => $_SESSION['user_role'],
            'users' => $users,
            'error' => $error,
            'success' => $success
        ]);
    }

    /**
     * Show edit user form
     */
    public function showEditUser(Request $request, Response $response): void {
        $this->requireAdmin();

        $id = (int)$request->get('id');
        if (!$id) {
            $this->redirect('/admin/users');
        }

        $user = $this->userModel->findById($id);
        if (!$user || $user['role'] === 'admin') {
            $_SESSION['manage_user_error'] = "User not found or cannot be edited.";
            $this->redirect('/admin/users');
        }

        $error = $_SESSION['edit_user_error'] ?? '';
        unset($_SESSION['edit_user_error']);

        $this->render('admin/edit-user', [
            'userRole' => $_SESSION['user_role'],
            'user' => $user,
            'error' => $error
        ]);
    }

    /**
     * Handle user update
     */
    public function handleEditUser(Request $request, Response $response): void {
        $this->requireAdmin();

        if (!$request->isPost()) {
            $this->redirect('/admin/users');
        }

        $id = (int)$request->post('id');
        $name = $request->cleanPost('name');
        $email = $request->cleanPost('email');
        $role = $request->cleanPost('role');

        if (empty($name) || empty($email) || empty($role)) {
            $_SESSION['edit_user_error'] = "All fields are required.";
            $this->redirect('/admin/users/edit?id=' . $id);
        }

        // Check if email belongs to another user
        $existing = $this->userModel->findByEmail($email);
        if ($existing && $existing['id'] !== $id) {
            $_SESSION['edit_user_error'] = "Email is already in use by another user.";
            $this->redirect('/admin/users/edit?id=' . $id);
        }

        try {
            $this->userModel->adminUpdateUser($id, $name, $email, $role);
            $_SESSION['manage_user_success'] = "User updated successfully.";
            $this->redirect('/admin/users');
        } catch (\Exception $e) {
            $_SESSION['edit_user_error'] = "Failed to update user: " . $e->getMessage();
            $this->redirect('/admin/users/edit?id=' . $id);
        }
    }

    /**
     * Handle user deletion
     */
    public function handleDeleteUser(Request $request, Response $response): void {
        $this->requireAdmin();

        if (!$request->isPost()) {
            $this->redirect('/admin/users');
        }

        $id = (int)$request->post('id');
        
        // Prevent deleting self
        if ($id === (int)$_SESSION['user_id']) {
            $_SESSION['manage_user_error'] = "You cannot delete your own account.";
            $this->redirect('/admin/users');
        }

        $user = $this->userModel->findById($id);
        if (!$user || $user['role'] === 'admin') {
            $_SESSION['manage_user_error'] = "Cannot delete admin users.";
            $this->redirect('/admin/users');
        }

        try {
            $this->userModel->delete($id);
            $_SESSION['manage_user_success'] = "User deleted successfully.";
        } catch (\Exception $e) {
            $_SESSION['manage_user_error'] = "Failed to delete user: " . $e->getMessage();
        }

        $this->redirect('/admin/users');
    }

    /**
     * List all courses
     */
    public function listCourses(Request $request, Response $response): void {
        $this->requireAdmin();

        $courses = $this->courseModel->getAll();
        
        $error = $_SESSION['manage_course_error'] ?? '';
        $success = $_SESSION['manage_course_success'] ?? '';
        unset($_SESSION['manage_course_error'], $_SESSION['manage_course_success']);

        $this->render('admin/courses-list', [
            'userRole' => $_SESSION['user_role'],
            'courses' => $courses,
            'error' => $error,
            'success' => $success
        ]);
    }

    /**
     * Show edit course form
     */
    public function showEditCourse(Request $request, Response $response): void {
        $this->requireAdmin();

        $id = (int)$request->get('id');
        if (!$id) {
            $this->redirect('/admin/courses');
        }

        $course = $this->courseModel->getById($id);
        if (!$course) {
            $_SESSION['manage_course_error'] = "Course not found.";
            $this->redirect('/admin/courses');
        }

        $db = \App\Core\Database::getConnection();
        $res = $db->query("SELECT id, name FROM departments");
        $departments = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];

        $error = $_SESSION['edit_course_error'] ?? '';
        unset($_SESSION['edit_course_error']);

        $this->render('admin/edit-course', [
            'userRole' => $_SESSION['user_role'],
            'course' => $course,
            'departments' => $departments,
            'error' => $error
        ]);
    }

    /**
     * Handle course update
     */
    public function handleEditCourse(Request $request, Response $response): void {
        $this->requireAdmin();

        if (!$request->isPost()) {
            $this->redirect('/admin/courses');
        }

        $id = (int)$request->post('id');
        $departmentId = (int)$request->post('department_id');
        $name = $request->cleanPost('name');
        $code = $request->cleanPost('code');
        $description = $request->cleanPost('description');
        $credits = (int)$request->post('credits');

        if (empty($name) || empty($code) || empty($departmentId)) {
            $_SESSION['edit_course_error'] = "Department, Name, and Code are required.";
            $this->redirect('/admin/courses/edit?id=' . $id);
        }

        try {
            $this->courseModel->update($id, $departmentId, $name, $code, $description, $credits ?: 3);
            $_SESSION['manage_course_success'] = "Course updated successfully.";
            $this->redirect('/admin/courses');
        } catch (\Exception $e) {
            $_SESSION['edit_course_error'] = "Failed to update course: " . $e->getMessage();
            $this->redirect('/admin/courses/edit?id=' . $id);
        }
    }

    /**
     * Handle course deletion
     */
    public function handleDeleteCourse(Request $request, Response $response): void {
        $this->requireAdmin();

        if (!$request->isPost()) {
            $this->redirect('/admin/courses');
        }

        $id = (int)$request->post('id');
        
        try {
            $this->courseModel->delete($id);
            $_SESSION['manage_course_success'] = "Course deleted successfully.";
        } catch (\Exception $e) {
            $_SESSION['manage_course_error'] = "Failed to delete course: " . $e->getMessage();
        }

        $this->redirect('/admin/courses');
    }
}
