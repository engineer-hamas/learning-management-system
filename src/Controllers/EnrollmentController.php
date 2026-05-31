<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;

class EnrollmentController extends Controller {
    private User $userModel;
    private Course $courseModel;
    private Enrollment $enrollmentModel;

    public function __construct() {
        $this->userModel = new User();
        $this->courseModel = new Course();
        $this->enrollmentModel = new Enrollment();
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
        }
    }

    public function index(Request $request, Response $response): void {
        $userId = $_SESSION['user_id'];
        $role = $_SESSION['user_role'];
        
        $data = [
            'userRole' => $role,
            'userName' => $_SESSION['user_name'],
            'userEmail' => $_SESSION['user_email'],
            'teacherSubjects' => [],
            'studentEnrollments' => [],
            'allStudents' => []
        ];

        // Fetch fresh avatar_path
        $user = $this->userModel->findById($userId);
        $data['userAvatar'] = $user['avatar_path'] ?? null;

        if ($role === 'teacher') {
            $teacherSubjects = $this->courseModel->getTaughtCourses($userId);
            
            foreach ($teacherSubjects as &$subject) {
                $subject['enrolled'] = $this->enrollmentModel->getEnrolledStudents($subject['id']);
            }
            $data['teacherSubjects'] = $teacherSubjects;
            
            // Fetch all students for the dropdown
            $data['allStudents'] = $this->userModel->getByRole('student');
        } else {
            // Student: Get their enrolled subjects
            $data['studentEnrollments'] = $this->courseModel->getEnrolledCourses($userId);
        }

        $data['error'] = $_SESSION['enroll_error'] ?? '';
        $data['success'] = $_SESSION['enroll_success'] ?? '';
        unset($_SESSION['enroll_error'], $_SESSION['enroll_success']);

        $this->render('enrollments', $data);
    }

    public function enrollStudent(Request $request, Response $response): void {
        if ($_SESSION['user_role'] !== 'teacher' || !$request->isPost()) {
            $this->redirect('/enrollments');
        }

        $studentId = (int)$request->post('student_id');
        $subjectId = (int)$request->post('subject_id');
        $teacherId = $_SESSION['user_id'];

        if (empty($studentId) || empty($subjectId)) {
            $_SESSION['enroll_error'] = "Student and Subject must be selected.";
            $this->redirect('/enrollments');
        }

        // Verify teacher actually teaches this subject
        if (!$this->courseModel->verifyTeacherAccess($teacherId, $subjectId)) {
            $_SESSION['enroll_error'] = "You do not have permission to enroll students in this subject.";
            $this->redirect('/enrollments');
        }

        // Enroll
        if ($this->enrollmentModel->isEnrolled($studentId, $subjectId)) {
            $_SESSION['enroll_error'] = "Student is already enrolled in this subject.";
        } else {
            if ($this->enrollmentModel->enrollStudent($studentId, $subjectId)) {
                $_SESSION['enroll_success'] = "Student successfully enrolled.";
            } else {
                $_SESSION['enroll_error'] = "Failed to enroll student.";
            }
        }
        $redirectUrl = $_SERVER['HTTP_REFERER'] ?? '/enrollments';
        $this->redirect($redirectUrl);
    }

    public function removeStudent(Request $request, Response $response): void {
        if ($_SESSION['user_role'] !== 'teacher' || !$request->isPost()) {
            $this->redirect('/enrollments');
        }

        $studentId = (int)$request->post('student_id');
        $subjectId = (int)$request->post('subject_id');
        $teacherId = $_SESSION['user_id'];

        if (empty($studentId) || empty($subjectId)) {
            $_SESSION['enroll_error'] = "Invalid request.";
            $this->redirect('/enrollments');
        }

        // Verify teacher actually teaches this subject
        if (!$this->courseModel->verifyTeacherAccess($teacherId, $subjectId)) {
            $_SESSION['enroll_error'] = "You do not have permission to remove students from this subject.";
            $this->redirect('/enrollments');
        }

        if ($this->enrollmentModel->unenrollStudent($studentId, $subjectId)) {
            $_SESSION['enroll_success'] = "Student successfully removed from the course.";
        } else {
            $_SESSION['enroll_error'] = "Failed to remove student.";
        }
        $redirectUrl = $_SERVER['HTTP_REFERER'] ?? '/enrollments';
        $this->redirect($redirectUrl);
    }
}
