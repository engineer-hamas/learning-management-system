<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Request;
use App\Core\Response;

class CourseController extends Controller {
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

    public function show(Request $request, Response $response): void {
        $courseId = (int)$request->get('id');
        if (!$courseId) {
            $this->redirect('/dashboard');
        }

        $userId = $_SESSION['user_id'];
        $role = $_SESSION['user_role'];

        // Strict Access Control check: Ensure teacher teaches it, or student is enrolled in it
        if ($role === 'teacher') {
            $checkStmt = $this->db->prepare("SELECT 1 FROM teacher_subjects WHERE teacher_id = ? AND subject_id = ?");
        } else {
            $checkStmt = $this->db->prepare("SELECT 1 FROM enrollments WHERE student_id = ? AND subject_id = ?");
        }
        $checkStmt->bind_param("ii", $userId, $courseId);
        $checkStmt->execute();
        $hasAccess = $checkStmt->get_result()->num_rows > 0;
        $checkStmt->close();

        if (!$hasAccess) {
            $this->redirect('/enrollments');
        }

        // Fetch Course Details
        $stmt = $this->db->prepare("SELECT id, name, code, description FROM subjects WHERE id = ?");
        $stmt->bind_param("i", $courseId);
        $stmt->execute();
        $course = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$course) {
            $this->redirect('/dashboard');
        }

        $data = [
            'userRole' => $role,
            'userName' => $_SESSION['user_name'],
            'userEmail' => $_SESSION['user_email'],
            'course' => $course,
            'materials' => [],
            'assignments' => [],
            'exams' => [],
            'quizzes' => []
        ];

        // Fetch user avatar
        $avatarStmt = $this->db->prepare("SELECT avatar_path FROM users WHERE id = ?");
        $avatarStmt->bind_param("i", $userId);
        $avatarStmt->execute();
        $avatarRes = $avatarStmt->get_result();
        $data['userAvatar'] = ($avatarRes && $avatarRes->num_rows > 0) ? $avatarRes->fetch_assoc()['avatar_path'] : null;
        $avatarStmt->close();

        // Query new course content tables
        $matStmt = $this->db->prepare("SELECT id, title, file_path, original_filename, created_at FROM course_materials WHERE subject_id = ? ORDER BY created_at DESC");
        $matStmt->bind_param("i", $courseId);
        $matStmt->execute();
        $matRes = $matStmt->get_result();
        if ($matRes) {
            while ($row = $matRes->fetch_assoc()) $data['materials'][] = $row;
        }
        $matStmt->close();

        $assStmt = $this->db->prepare("SELECT id, title, description, due_date, file_path, original_filename FROM assignments WHERE subject_id = ? ORDER BY due_date ASC");
        $assStmt->bind_param("i", $courseId);
        $assStmt->execute();
        $assRes = $assStmt->get_result();
        if ($assRes) {
            while ($row = $assRes->fetch_assoc()) $data['assignments'][] = $row;
        }
        $assStmt->close();

        $exStmt = $this->db->prepare("SELECT id, title, exam_date, duration_minutes, file_path, original_filename FROM exams WHERE subject_id = ? ORDER BY exam_date ASC");
        $exStmt->bind_param("i", $courseId);
        $exStmt->execute();
        $exRes = $exStmt->get_result();
        if ($exRes) {
            while ($row = $exRes->fetch_assoc()) $data['exams'][] = $row;
        }
        $exStmt->close();

        $qzStmt = $this->db->prepare("SELECT id, title, description, file_path, original_filename, created_at FROM quizzes WHERE subject_id = ? ORDER BY created_at DESC");
        $qzStmt->bind_param("i", $courseId);
        $qzStmt->execute();
        $qzRes = $qzStmt->get_result();
        if ($qzRes) {
            while ($row = $qzRes->fetch_assoc()) $data['quizzes'][] = $row;
        }
        $qzStmt->close();

        $this->render('course', $data);
    }

    private function verifyTeacherAccess(int $courseId): bool {
        if ($_SESSION['user_role'] !== 'teacher') return false;
        $stmt = $this->db->prepare("SELECT 1 FROM teacher_subjects WHERE teacher_id = ? AND subject_id = ?");
        $stmt->bind_param("ii", $_SESSION['user_id'], $courseId);
        $stmt->execute();
        $hasAccess = $stmt->get_result()->num_rows > 0;
        $stmt->close();
        return $hasAccess;
    }

    private function handleFileUpload(string $subDir, string $savedFilename): array {
        if (!isset($_FILES['content_file']) || $_FILES['content_file']['error'] !== UPLOAD_ERR_OK) {
            return [null, null];
        }
        $uploadDir = dirname(dirname(__DIR__)) . '/public/uploads/' . $subDir;
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
        
        $fileInfo = pathinfo($_FILES['content_file']['name']);
        $ext = $fileInfo['extension'] ?? '';
        
        if (!empty($savedFilename)) {
            $originalName = preg_replace('/[^a-zA-Z0-9_ -]/', '', $savedFilename);
            if ($ext && !preg_match('/\.' . preg_quote($ext, '/') . '$/i', $originalName)) {
                $originalName .= '.' . $ext;
            }
        } else {
            $originalName = $_FILES['content_file']['name'];
        }
        
        $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9_-]/', '', $fileInfo['filename']) . ($ext ? '.' . $ext : '');
        $targetPath = $uploadDir . '/' . $filename;
        
        if (move_uploaded_file($_FILES['content_file']['tmp_name'], $targetPath)) {
            return ['/uploads/' . $subDir . '/' . $filename, $originalName];
        }
        return [null, null];
    }

    public function addMaterial(Request $request, Response $response): void {
        $courseId = (int)$request->post('subject_id');
        if (!$this->verifyTeacherAccess($courseId)) $this->redirect('/course?id=' . $courseId);

        $title = $request->cleanPost('title');
        $savedFilename = $request->cleanPost('saved_filename');
        
        list($dbPath, $originalName) = $this->handleFileUpload('materials', $savedFilename);
        
        if ($dbPath) {
            $stmt = $this->db->prepare("INSERT INTO course_materials (subject_id, teacher_id, title, file_path, original_filename) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("iisss", $courseId, $_SESSION['user_id'], $title, $dbPath, $originalName);
            $stmt->execute();
            $stmt->close();
        }
        $this->redirect('/course?id=' . $courseId);
    }

    public function addAssignment(Request $request, Response $response): void {
        $courseId = (int)$request->post('subject_id');
        if (!$this->verifyTeacherAccess($courseId)) $this->redirect('/course?id=' . $courseId);

        $title = $request->cleanPost('title');
        $desc = $request->cleanPost('description');
        $dueDate = $request->cleanPost('due_date');
        $savedFilename = $request->cleanPost('saved_filename');

        if ($dueDate) {
            $dueDate = str_replace('T', ' ', $dueDate) . ':00';
        } else {
            $dueDate = null;
        }

        list($dbPath, $originalName) = $this->handleFileUpload('assignments', $savedFilename);

        $stmt = $this->db->prepare("INSERT INTO assignments (subject_id, teacher_id, title, description, due_date, file_path, original_filename) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iisssss", $courseId, $_SESSION['user_id'], $title, $desc, $dueDate, $dbPath, $originalName);
        $stmt->execute();
        $stmt->close();
        
        $this->redirect('/course?id=' . $courseId);
    }

    public function addExam(Request $request, Response $response): void {
        $courseId = (int)$request->post('subject_id');
        if (!$this->verifyTeacherAccess($courseId)) $this->redirect('/course?id=' . $courseId);

        $title = $request->cleanPost('title');
        $examDate = $request->cleanPost('exam_date');
        $duration = (int)$request->post('duration_minutes');
        $savedFilename = $request->cleanPost('saved_filename');

        if ($examDate) {
            $examDate = str_replace('T', ' ', $examDate) . ':00';
            
            list($dbPath, $originalName) = $this->handleFileUpload('exams', $savedFilename);
            
            $stmt = $this->db->prepare("INSERT INTO exams (subject_id, teacher_id, title, exam_date, duration_minutes, file_path, original_filename) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("iississ", $courseId, $_SESSION['user_id'], $title, $examDate, $duration, $dbPath, $originalName);
            $stmt->execute();
            $stmt->close();
        }
        
        $this->redirect('/course?id=' . $courseId);
    }

    public function addQuiz(Request $request, Response $response): void {
        $courseId = (int)$request->post('subject_id');
        if (!$this->verifyTeacherAccess($courseId)) $this->redirect('/course?id=' . $courseId);

        $title = $request->cleanPost('title');
        $desc = $request->cleanPost('description');
        $savedFilename = $request->cleanPost('saved_filename');

        list($dbPath, $originalName) = $this->handleFileUpload('quizzes', $savedFilename);

        $stmt = $this->db->prepare("INSERT INTO quizzes (subject_id, teacher_id, title, description, file_path, original_filename) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iissss", $courseId, $_SESSION['user_id'], $title, $desc, $dbPath, $originalName);
        $stmt->execute();
        $stmt->close();
        
        $this->redirect('/course?id=' . $courseId);
    }

    private function deletePhysicalFile(string $table, int $id, int $courseId): void {
        $stmt = $this->db->prepare("SELECT file_path FROM " . $table . " WHERE id = ? AND subject_id = ?");
        $stmt->bind_param("ii", $id, $courseId);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($row = $res->fetch_assoc()) {
            if (!empty($row['file_path'])) {
                $filePath = dirname(dirname(__DIR__)) . '/public' . $row['file_path'];
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
        }
        $stmt->close();
    }

    public function deleteMaterial(Request $request, Response $response): void {
        $courseId = (int)$request->post('subject_id');
        if (!$this->verifyTeacherAccess($courseId)) $this->redirect('/course?id=' . $courseId);
        $id = (int)$request->post('id');
        
        $this->deletePhysicalFile('course_materials', $id, $courseId);

        $stmt = $this->db->prepare("DELETE FROM course_materials WHERE id = ? AND subject_id = ?");
        $stmt->bind_param("ii", $id, $courseId);
        $stmt->execute();
        $stmt->close();
        $this->redirect('/course?id=' . $courseId);
    }

    public function deleteAssignment(Request $request, Response $response): void {
        $courseId = (int)$request->post('subject_id');
        if (!$this->verifyTeacherAccess($courseId)) $this->redirect('/course?id=' . $courseId);
        $id = (int)$request->post('id');
        
        $this->deletePhysicalFile('assignments', $id, $courseId);

        $stmt = $this->db->prepare("DELETE FROM assignments WHERE id = ? AND subject_id = ?");
        $stmt->bind_param("ii", $id, $courseId);
        $stmt->execute();
        $stmt->close();
        $this->redirect('/course?id=' . $courseId);
    }

    public function deleteExam(Request $request, Response $response): void {
        $courseId = (int)$request->post('subject_id');
        if (!$this->verifyTeacherAccess($courseId)) $this->redirect('/course?id=' . $courseId);
        $id = (int)$request->post('id');
        
        $this->deletePhysicalFile('exams', $id, $courseId);

        $stmt = $this->db->prepare("DELETE FROM exams WHERE id = ? AND subject_id = ?");
        $stmt->bind_param("ii", $id, $courseId);
        $stmt->execute();
        $stmt->close();
        $this->redirect('/course?id=' . $courseId);
    }

    public function deleteQuiz(Request $request, Response $response): void {
        $courseId = (int)$request->post('subject_id');
        if (!$this->verifyTeacherAccess($courseId)) $this->redirect('/course?id=' . $courseId);
        $id = (int)$request->post('id');
        
        $this->deletePhysicalFile('quizzes', $id, $courseId);

        $stmt = $this->db->prepare("DELETE FROM quizzes WHERE id = ? AND subject_id = ?");
        $stmt->bind_param("ii", $id, $courseId);
        $stmt->execute();
        $stmt->close();
        $this->redirect('/course?id=' . $courseId);
    }

    public function showAttendance(Request $request, Response $response): void {
        $courseId = (int)$request->get('id');
        if (!$this->verifyTeacherAccess($courseId) && !$this->verifyStudentEnrollment($courseId)) {
            $this->redirect('/dashboard');
        }

        $stmt = $this->db->prepare("SELECT id, name, code, description FROM subjects WHERE id = ?");
        $stmt->bind_param("i", $courseId);
        $stmt->execute();
        $res = $stmt->get_result();
        $course = $res->fetch_assoc();
        $stmt->close();

        if (!$course) $this->redirect('/dashboard');

        $data = [
            'userRole' => $_SESSION['role'],
            'userName' => $_SESSION['name'],
            'course' => $course,
        ];

        // Avatar
        $avatarStmt = $this->db->prepare("SELECT avatar_path FROM users WHERE id = ?");
        $avatarStmt->bind_param("i", $_SESSION['user_id']);
        $avatarStmt->execute();
        $avatarRes = $avatarStmt->get_result();
        $data['userAvatar'] = ($avatarRes && $avatarRes->num_rows > 0) ? $avatarRes->fetch_assoc()['avatar_path'] : null;
        $avatarStmt->close();

        if ($_SESSION['role'] === 'teacher') {
            // Get all enrolled students
            $stuStmt = $this->db->prepare("SELECT u.id, u.name, u.email, u.roll_no, u.avatar_path FROM users u JOIN enrollments e ON u.id = e.student_id WHERE e.subject_id = ? ORDER BY u.name ASC");
            $stuStmt->bind_param("i", $courseId);
            $stuStmt->execute();
            $data['students'] = $stuStmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $stuStmt->close();

            // Get attendance dates
            $dateStmt = $this->db->prepare("SELECT DISTINCT attendance_date FROM attendance WHERE subject_id = ? ORDER BY attendance_date DESC LIMIT 15");
            $dateStmt->bind_param("i", $courseId);
            $dateStmt->execute();
            $dates = [];
            $res = $dateStmt->get_result();
            while($r = $res->fetch_assoc()) $dates[] = $r['attendance_date'];
            $dateStmt->close();
            $data['attendance_dates'] = $dates;

            // Get attendance matrix
            $matrix = [];
            if (!empty($dates)) {
                $matStmt = $this->db->prepare("SELECT student_id, attendance_date, status FROM attendance WHERE subject_id = ? AND attendance_date >= ? AND attendance_date <= ?");
                $minDate = end($dates);
                $maxDate = $dates[0];
                $matStmt->bind_param("iss", $courseId, $minDate, $maxDate);
                $matStmt->execute();
                $res = $matStmt->get_result();
                while($r = $res->fetch_assoc()) {
                    $matrix[$r['student_id']][$r['attendance_date']] = $r['status'];
                }
                $matStmt->close();
            }
            $data['attendance_matrix'] = $matrix;

        } else {
            // Student View
            $logStmt = $this->db->prepare("SELECT attendance_date as date, status FROM attendance WHERE subject_id = ? AND student_id = ? ORDER BY attendance_date DESC");
            $logStmt->bind_param("ii", $courseId, $_SESSION['user_id']);
            $logStmt->execute();
            $data['attendance_log'] = $logStmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $logStmt->close();

            $total = count($data['attendance_log']);
            $present = $late = $absent = 0;
            foreach ($data['attendance_log'] as $l) {
                if ($l['status'] === 'present') $present++;
                if ($l['status'] === 'late') $late++;
                if ($l['status'] === 'absent') $absent++;
            }
            $percentage = $total > 0 ? round((($present + $late) / $total) * 100) : 100;

            $data['attendance_summary'] = [
                'total' => $total,
                'present' => $present,
                'late' => $late,
                'absent' => $absent,
                'percentage' => $percentage
            ];
        }

        $this->render('attendance', $data);
    }

    public function takeAttendance(Request $request, Response $response): void {
        $courseId = (int)$request->get('id');
        if (!$this->verifyTeacherAccess($courseId)) $this->redirect('/dashboard');

        $stmt = $this->db->prepare("SELECT id, name, code, description FROM subjects WHERE id = ?");
        $stmt->bind_param("i", $courseId);
        $stmt->execute();
        $course = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$course) $this->redirect('/dashboard');

        $data = [
            'userRole' => $_SESSION['role'],
            'userName' => $_SESSION['name'],
            'course' => $course,
        ];

        // Avatar
        $avatarStmt = $this->db->prepare("SELECT avatar_path FROM users WHERE id = ?");
        $avatarStmt->bind_param("i", $_SESSION['user_id']);
        $avatarStmt->execute();
        $avatarRes = $avatarStmt->get_result();
        $data['userAvatar'] = ($avatarRes && $avatarRes->num_rows > 0) ? $avatarRes->fetch_assoc()['avatar_path'] : null;
        $avatarStmt->close();

        // Get all enrolled students
        $stuStmt = $this->db->prepare("SELECT u.id, u.name, u.email, u.roll_no, u.avatar_path FROM users u JOIN enrollments e ON u.id = e.student_id WHERE e.subject_id = ? ORDER BY u.name ASC");
        $stuStmt->bind_param("i", $courseId);
        $stuStmt->execute();
        $data['enrolled_students'] = $stuStmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stuStmt->close();

        $this->render('take-attendance', $data);
    }

    public function markAttendance(Request $request, Response $response): void {
        $courseId = (int)$request->post('subject_id');
        if (!$this->verifyTeacherAccess($courseId)) $this->redirect('/dashboard');

        $date = $request->post('attendance_date');
        $attendanceData = $request->post('attendance') ?? []; // array of student_id => status

        if (!empty($date) && !empty($attendanceData)) {
            // Check if record exists
            $checkStmt = $this->db->prepare("SELECT id FROM attendance WHERE subject_id = ? AND student_id = ? AND attendance_date = ?");
            $updateStmt = $this->db->prepare("UPDATE attendance SET status = ? WHERE id = ?");
            $insertStmt = $this->db->prepare("INSERT INTO attendance (subject_id, student_id, attendance_date, status) VALUES (?, ?, ?, ?)");

            foreach ($attendanceData as $studentId => $status) {
                $studentId = (int)$studentId;
                if (!in_array($status, ['present', 'late', 'absent'])) continue;

                $checkStmt->bind_param("iis", $courseId, $studentId, $date);
                $checkStmt->execute();
                $res = $checkStmt->get_result();
                
                if ($row = $res->fetch_assoc()) {
                    $updateStmt->bind_param("si", $status, $row['id']);
                    $updateStmt->execute();
                } else {
                    $insertStmt->bind_param("iiss", $courseId, $studentId, $date, $status);
                    $insertStmt->execute();
                }
            }
            $checkStmt->close();
            $updateStmt->close();
            $insertStmt->close();
        }

        $this->redirect('/course/attendance?id=' . $courseId);
    }
}