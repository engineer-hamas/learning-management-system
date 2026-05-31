<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Request;
use App\Core\Response;

use App\Models\User;

/**
 * Class ProfileController
 * 
 * Manages user profile settings, secure file uploads, role-specific mappings, and account security.
 */
class ProfileController extends Controller {
    private User $userModel;
    private \mysqli $db;

    public function __construct() {
        $this->userModel = new User();
        $this->db = \App\Core\Database::getConnection();
    }

    /**
     * Helper to authenticate current session
     */
    private function authenticateSession(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
        }
    }

    /**
     * Show Profile Settings Page
     * 
     * @param Request $request
     * @param Response $response
     */
    public function showSettings(Request $request, Response $response): void {
        $this->authenticateSession();

        $userId = $_SESSION['user_id'];

        // 1. Fetch fresh user info from the database
        $user = $this->userModel->getFullProfile($userId);
        
        if (!$user) {
            die("User record not found.");
        }
        
        // Map the correct department_id based on role
        if ($user['role'] === 'teacher') {
            $user['department_id'] = $user['t_dept'];
        } else {
            $user['department_id'] = $user['s_dept'];
        }

        // 2. Fetch all departments (dynamic majors/departments list)
        $depResult = $this->db->query("SELECT id, name, code FROM departments ORDER BY name ASC");
        $departments = [];
        if ($depResult) {
            while ($row = $depResult->fetch_assoc()) {
                $departments[] = $row;
            }
        }

        // 3. Fetch all subjects offered
        $subResult = $this->db->query("SELECT id, department_id, name, code, credits FROM subjects ORDER BY code ASC");
        $subjects = [];
        if ($subResult) {
            while ($row = $subResult->fetch_assoc()) {
                $subjects[] = $row;
            }
        }

        // 4. Fetch user's checked subjects depending on role
        $userSubjectIds = [];
        if ($user['role'] === 'teacher') {
            $subStmt = $this->db->prepare("SELECT subject_id FROM teacher_subjects WHERE teacher_id = ?");
        } else {
            $subStmt = $this->db->prepare("SELECT subject_id FROM enrollments WHERE student_id = ?");
        }
        
        if ($subStmt) {
            $subStmt->bind_param("i", $userId);
            $subStmt->execute();
            $subRes = $subStmt->get_result();
            while ($row = $subRes->fetch_assoc()) {
                $userSubjectIds[] = (int)$row['subject_id'];
            }
            $subStmt->close();
        }

        // 5. Gather notification messages and active tabs from flash session variables
        $error = $_SESSION['settings_error'] ?? '';
        $success = $_SESSION['settings_success'] ?? '';
        $activeTab = $_SESSION['settings_active_tab'] ?? 'general';
        unset($_SESSION['settings_error'], $_SESSION['settings_success'], $_SESSION['settings_active_tab']);

        // 6. Render
        $this->render('settings', [
            'user' => $user,
            'userRole' => $user['role'],
            'userName' => $user['name'],
            'userEmail' => $user['email'],
            'userAvatar' => $user['avatar_path'],
            'departments' => $departments,
            'subjects' => $subjects,
            'userSubjectIds' => $userSubjectIds,
            'error' => $error,
            'success' => $success,
            'activeTab' => $activeTab
        ]);
    }

    /**
     * Handle Profile Information Update
     * 
     * @param Request $request
     * @param Response $response
     */
    public function handleProfileUpdate(Request $request, Response $response): void {
        $this->authenticateSession();

        if (!$request->isPost()) {
            $this->redirect('/settings');
        }

        $userId = $_SESSION['user_id'];
        $role = $_SESSION['user_role'];

        if (isset($_POST['is_general_profile'])) {
            // ==========================================
            // MODE A: Update General Profile Information
            // ==========================================
            $name = $request->cleanPost('name');
            $email = $request->cleanPost('email');
            $phone = $request->cleanPost('phone');
            $bio = $request->cleanPost('bio');

            // Basic fields validations
            if (empty($name) || empty($email)) {
                $_SESSION['settings_error'] = "Name and email are required fields.";
                $_SESSION['settings_active_tab'] = "general";
                $this->redirect('/settings');
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['settings_error'] = "Invalid email format.";
                $_SESSION['settings_active_tab'] = "general";
                $this->redirect('/settings');
            }

            // Check email uniqueness for other users
            $existingUser = $this->userModel->findByEmail($email);
            if ($existingUser && $existingUser['id'] !== $userId) {
                $_SESSION['settings_error'] = "This email is already in use by another account.";
                $_SESSION['settings_active_tab'] = "general";
                $this->redirect('/settings');
            }

            // 1. Process custom Profile Image Upload
            $avatarPath = null;
            if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] !== UPLOAD_ERR_NO_FILE) {
                $file = $_FILES['avatar'];

                if ($file['error'] !== UPLOAD_ERR_OK) {
                    $_SESSION['settings_error'] = "File upload error code: " . $file['error'];
                    $_SESSION['settings_active_tab'] = "general";
                    $this->redirect('/settings');
                }

                // Size Guard (2MB Limit)
                if ($file['size'] > 2 * 1024 * 1024) {
                    $_SESSION['settings_error'] = "Profile image must be smaller than 2MB.";
                    $_SESSION['settings_active_tab'] = "general";
                    $this->redirect('/settings');
                }

                // MIME Type Guard
                $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                $fileMime = mime_content_type($file['tmp_name']);
                if (!in_array($fileMime, $allowedMimeTypes)) {
                    $_SESSION['settings_error'] = "Invalid image file format. Allowed formats: JPG, PNG, GIF, WEBP.";
                    $_SESSION['settings_active_tab'] = "general";
                    $this->redirect('/settings');
                }

                // Rename and move securely
                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                if (empty($ext)) {
                    $ext = 'jpg';
                }
                $secureFilename = uniqid('avatar_', true) . '.' . $ext;
                
                $publicUploadPath = '/uploads/avatars/' . $secureFilename;
                $absoluteUploadPath = dirname(dirname(__DIR__)) . '/public' . $publicUploadPath;

                if (move_uploaded_file($file['tmp_name'], $absoluteUploadPath)) {
                    // Delete previous avatar file from disk if it exists
                    $prevStmt = $this->db->prepare("SELECT avatar_path FROM users WHERE id = ?");
                    $prevStmt->bind_param("i", $userId);
                    $prevStmt->execute();
                    $prevRes = $prevStmt->get_result();
                    if ($prevRes->num_rows === 1) {
                        $prevUser = $prevRes->fetch_assoc();
                        $prevPath = $prevUser['avatar_path'];
                        if (!empty($prevPath)) {
                            $prevFullPath = dirname(dirname(__DIR__)) . '/public' . $prevPath;
                            if (file_exists($prevFullPath)) {
                                @unlink($prevFullPath);
                            }
                        }
                    }
                    $prevStmt->close();

                    $avatarPath = $publicUploadPath;
                } else {
                    $_SESSION['settings_error'] = "Failed to save profile picture file. Please check folder permissions.";
                    $_SESSION['settings_active_tab'] = "general";
                    $this->redirect('/settings');
                }
            }

            // Update database record for general fields
            $success = $this->userModel->updateGeneralProfile($userId, $name, $email, $phone, $bio, $avatarPath);

            if (!$success) {
                $_SESSION['settings_error'] = "Failed to update profile settings.";
                $_SESSION['settings_active_tab'] = "general";
                $this->redirect('/settings');
            }

            // Update session values
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;

            $_SESSION['settings_success'] = "General profile details successfully saved.";
            $_SESSION['settings_active_tab'] = "general";
            $this->redirect('/settings');
        } else {
            // ==========================================
            // MODE B: Update Professional / Academic
            // ==========================================
            $depId = $request->cleanPost('department_id');
            $departmentId = !empty($depId) ? (int)$depId : null;

            if ($role === 'teacher') {
                $title = $request->cleanPost('title');
                $officeHours = $request->cleanPost('office_hours');
                $specialization = $request->cleanPost('specialization');

                $success = $this->userModel->updateTeacherProfile($userId, $departmentId, $title, $officeHours, $specialization);
            } else {
                $studentId = $request->cleanPost('student_id');
                $expectedGraduation = $request->cleanPost('expected_graduation');
                $studyStyle = $request->cleanPost('study_style');

                $success = $this->userModel->updateStudentProfile($userId, $departmentId, $studentId, $expectedGraduation, $studyStyle);
            }

            if (!$success) {
                $_SESSION['settings_error'] = "Failed to update academic/professional settings.";
                $_SESSION['settings_active_tab'] = $role === 'teacher' ? 'teacher-profile' : 'student-profile';
                $this->redirect('/settings');
            }

            // Update many-to-many relationship mapping for dynamic subjects checkmarks
            $checkedSubjectIds = isset($_POST['subjects']) ? array_map('intval', $_POST['subjects']) : [];

            if ($role === 'teacher') {
                // Teacher mapping synchronization
                $delStmt = $this->db->prepare("DELETE FROM teacher_subjects WHERE teacher_id = ?");
                $delStmt->bind_param("i", $userId);
                $delStmt->execute();
                $delStmt->close();

                if (!empty($checkedSubjectIds)) {
                    $insStmt = $this->db->prepare("INSERT INTO teacher_subjects (teacher_id, subject_id) VALUES (?, ?)");
                    foreach ($checkedSubjectIds as $subId) {
                        $insStmt->bind_param("ii", $userId, $subId);
                        $insStmt->execute();
                    }
                    $insStmt->close();
                }
            } else {
                // Students can no longer modify their enrollments via settings.
                // This is strictly managed by teachers.
            }

            $_SESSION['settings_success'] = "Academic/Professional details successfully saved.";
            $_SESSION['settings_active_tab'] = $role === 'teacher' ? 'teacher-profile' : 'student-profile';
            $this->redirect('/settings');
        }
    }

    /**
     * Handle Password Reset Action
     * 
     * @param Request $request
     * @param Response $response
     */
    public function handlePasswordUpdate(Request $request, Response $response): void {
        $this->authenticateSession();

        if (!$request->isPost()) {
            $this->redirect('/settings');
        }

        $userId = $_SESSION['user_id'];

        $currentPassword = $request->post('current_password');
        $newPassword = $request->post('new_password');
        $confirmPassword = $request->post('confirm_password');

        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            $_SESSION['settings_error'] = "All password fields are required.";
            $_SESSION['settings_active_tab'] = "security";
            $this->redirect('/settings');
        }

        if ($newPassword !== $confirmPassword) {
            $_SESSION['settings_error'] = "New passwords do not match.";
            $_SESSION['settings_active_tab'] = "security";
            $this->redirect('/settings');
        }

        if (strlen($newPassword) < 8) {
            $_SESSION['settings_error'] = "New password must be at least 8 characters long.";
            $_SESSION['settings_active_tab'] = "security";
            $this->redirect('/settings');
        }

        $user = $this->userModel->findByEmail($_SESSION['user_email']);
        
        if (!$user) {
            die("User details not found.");
        }

        if (!password_verify($currentPassword, $user['password'])) {
            $_SESSION['settings_error'] = "Current password is incorrect.";
            $_SESSION['settings_active_tab'] = "security";
            $this->redirect('/settings');
        }

        // Hashing and UPDATE execution
        $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
        $success = $this->userModel->updatePassword($userId, $newHash);
        
        if ($success) {
            $_SESSION['settings_success'] = "Password changed successfully.";
        } else {
            $_SESSION['settings_error'] = "Failed to update password.";
        }

        $_SESSION['settings_active_tab'] = "security";
        $this->redirect('/settings');
    }

    /**
     * Handle Notification preference changes
     * 
     * @param Request $request
     * @param Response $response
     */
    public function handleNotificationsUpdate(Request $request, Response $response): void {
        $this->authenticateSession();

        if (!$request->isPost()) {
            $this->redirect('/settings');
        }

        $userId = $_SESSION['user_id'];

        $notifyEmail = isset($_POST['notify_email']) ? 1 : 0;
        $notifyClassAlerts = isset($_POST['notify_class_alerts']) ? 1 : 0;
        $notifyMarketing = isset($_POST['notify_marketing']) ? 1 : 0;

        $success = $this->userModel->updateNotificationSettings($userId, $notifyEmail, $notifyClassAlerts, $notifyMarketing);

        if ($success) {
            $_SESSION['settings_success'] = "Notification preferences updated.";
        } else {
            $_SESSION['settings_error'] = "Failed to update notification settings.";
        }

        $_SESSION['settings_active_tab'] = "notifications";
        $this->redirect('/settings');
    }
}
