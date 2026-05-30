<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Request;
use App\Core\Response;

class EnrollmentController extends Controller {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
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
        $stmt = $this->db->prepare("SELECT avatar_path FROM users WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $res = $stmt->get_result();
        $data['userAvatar'] = ($res && $res->num_rows > 0) ? $res->fetch_assoc()['avatar_path'] : null;
        $stmt->close();

        if ($role === 'teacher') {
            // Get all subjects this teacher teaches
            $stmt = $this->db->prepare("
                SELECT s.id, s.name, s.code 
                FROM subjects s
                JOIN teacher_subjects ts ON s.id = ts.subject_id
                WHERE ts.teacher_id = ?
            ");
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $subjectsResult = $stmt->get_result();
            
            while ($subject = $subjectsResult->fetch_assoc()) {
                $subjectId = $subject['id'];
                // Get students enrolled in this subject
                $enrolledStmt = $this->db->prepare("
                    SELECT u.id, u.name, u.email, e.enrolled_at
                    FROM users u
                    JOIN enrollments e ON u.id = e.student_id
                    WHERE e.subject_id = ?
                ");
                $enrolledStmt->bind_param("i", $subjectId);
                $enrolledStmt->execute();
                $enrolledRes = $enrolledStmt->get_result();
                $subject['enrolled'] = [];
                while ($student = $enrolledRes->fetch_assoc()) {
                    $subject['enrolled'][] = $student;
                }
                $enrolledStmt->close();
                
                $data['teacherSubjects'][] = $subject;
            }
            $stmt->close();
            
            // Fetch all students for the dropdown
            $stuRes = $this->db->query("SELECT id, name, email FROM users WHERE role = 'student'");
            if ($stuRes) {
                while ($stu = $stuRes->fetch_assoc()) {
                    $data['allStudents'][] = $stu;
                }
            }
        } else {
            // Student: Get their enrolled subjects
            $stmt = $this->db->prepare("
                SELECT s.id, s.name, s.code, e.enrolled_at
                FROM subjects s
                JOIN enrollments e ON s.id = e.subject_id
                WHERE e.student_id = ?
            ");
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $enrolledResult = $stmt->get_result();
            while ($row = $enrolledResult->fetch_assoc()) {
                $data['studentEnrollments'][] = $row;
            }
            $stmt->close();
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
        $checkStmt = $this->db->prepare("SELECT 1 FROM teacher_subjects WHERE teacher_id = ? AND subject_id = ?");
        $checkStmt->bind_param("ii", $teacherId, $subjectId);
        $checkStmt->execute();
        $isTeaching = $checkStmt->get_result()->num_rows > 0;
        $checkStmt->close();

        if (!$isTeaching) {
            $_SESSION['enroll_error'] = "You do not have permission to enroll students in this subject.";
            $this->redirect('/enrollments');
        }

        // Enroll
        $insStmt = $this->db->prepare("INSERT IGNORE INTO enrollments (student_id, subject_id) VALUES (?, ?)");
        $insStmt->bind_param("ii", $studentId, $subjectId);
        
        if ($insStmt->execute()) {
            if ($insStmt->affected_rows > 0) {
                $_SESSION['enroll_success'] = "Student successfully enrolled.";
            } else {
                $_SESSION['enroll_error'] = "Student is already enrolled in this subject.";
            }
        } else {
            $_SESSION['enroll_error'] = "Failed to enroll student.";
        }
        $insStmt->close();
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
        $checkStmt = $this->db->prepare("SELECT 1 FROM teacher_subjects WHERE teacher_id = ? AND subject_id = ?");
        $checkStmt->bind_param("ii", $teacherId, $subjectId);
        $checkStmt->execute();
        $isTeaching = $checkStmt->get_result()->num_rows > 0;
        $checkStmt->close();

        if (!$isTeaching) {
            $_SESSION['enroll_error'] = "You do not have permission to remove students from this subject.";
            $this->redirect('/enrollments');
        }

        $delStmt = $this->db->prepare("DELETE FROM enrollments WHERE student_id = ? AND subject_id = ?");
        $delStmt->bind_param("ii", $studentId, $subjectId);
        
        if ($delStmt->execute()) {
            $_SESSION['enroll_success'] = "Student successfully removed from the course.";
        } else {
            $_SESSION['enroll_error'] = "Failed to remove student.";
        }
        $delStmt->close();
        $redirectUrl = $_SERVER['HTTP_REFERER'] ?? '/enrollments';
        $this->redirect($redirectUrl);
    }
}
