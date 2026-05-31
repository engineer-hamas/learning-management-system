<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Models\User;
use App\Models\Course;
use App\Models\CourseMaterial;
use App\Models\Enrollment;
use App\Models\Attendance;

class CourseController extends Controller {
    private User $userModel;
    private Course $courseModel;
    private CourseMaterial $materialModel;
    private Enrollment $enrollmentModel;
    private Attendance $attendanceModel;

    public function __construct() {
        $this->userModel = new User();
        $this->courseModel = new Course();
        $this->materialModel = new CourseMaterial();
        $this->enrollmentModel = new Enrollment();
        $this->attendanceModel = new Attendance();

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

        if ($role === 'teacher') {
            $hasAccess = $this->courseModel->verifyTeacherAccess($userId, $courseId);
        } else {
            $hasAccess = $this->courseModel->verifyStudentEnrollment($userId, $courseId);
        }

        if (!$hasAccess) {
            $this->redirect('/enrollments');
        }

        $course = $this->courseModel->getById($courseId);
        if (!$course) {
            $this->redirect('/dashboard');
        }

        $user = $this->userModel->findById($userId);

        $data = [
            'userRole' => $role,
            'userName' => $_SESSION['user_name'],
            'userEmail' => $_SESSION['user_email'],
            'userAvatar' => $user['avatar_path'] ?? null,
            'course' => $course,
            'materials' => $this->materialModel->getMaterials($courseId),
            'assignments' => $this->materialModel->getAssignments($courseId),
            'exams' => $this->materialModel->getExams($courseId),
            'quizzes' => $this->materialModel->getQuizzes($courseId),
            'enrolledStudents' => $this->enrollmentModel->getEnrolledStudents($courseId),
            'allStudents' => $role === 'teacher' ? $this->userModel->getByRole('student') : []
        ];

        $this->render('course', $data);
    }

    private function verifyTeacherAccess(int $courseId): bool {
        if ($_SESSION['user_role'] !== 'teacher') return false;
        return $this->courseModel->verifyTeacherAccess($_SESSION['user_id'], $courseId);
    }

    private function verifyStudentEnrollment(int $courseId): bool {
        if ($_SESSION['user_role'] !== 'student') return false;
        return $this->courseModel->verifyStudentEnrollment($_SESSION['user_id'], $courseId);
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
            $this->materialModel->addMaterial($courseId, $_SESSION['user_id'], $title, $dbPath, $originalName);
        }
        $redirectUrl = $_SERVER['HTTP_REFERER'] ?? '/course?id=' . $courseId;
        $this->redirect($redirectUrl);
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

        $this->materialModel->addAssignment($courseId, $_SESSION['user_id'], $title, $desc, $dueDate, $dbPath, $originalName);
        
        $redirectUrl = $_SERVER['HTTP_REFERER'] ?? '/course?id=' . $courseId;
        $this->redirect($redirectUrl);
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
            $this->materialModel->addExam($courseId, $_SESSION['user_id'], $title, $examDate, $duration, $dbPath, $originalName);
        }
        
        $redirectUrl = $_SERVER['HTTP_REFERER'] ?? '/course?id=' . $courseId;
        $this->redirect($redirectUrl);
    }

    public function addQuiz(Request $request, Response $response): void {
        $courseId = (int)$request->post('subject_id');
        if (!$this->verifyTeacherAccess($courseId)) $this->redirect('/course?id=' . $courseId);

        $title = $request->cleanPost('title');
        $desc = $request->cleanPost('description');
        $savedFilename = $request->cleanPost('saved_filename');

        list($dbPath, $originalName) = $this->handleFileUpload('quizzes', $savedFilename);

        $this->materialModel->addQuiz($courseId, $_SESSION['user_id'], $title, $desc, $dbPath, $originalName);
        
        $redirectUrl = $_SERVER['HTTP_REFERER'] ?? '/course?id=' . $courseId;
        $this->redirect($redirectUrl);
    }

    private function deletePhysicalFile(string $table, int $id, int $courseId): void {
        $db = \App\Core\Database::getConnection();
        $stmt = $db->prepare("SELECT file_path FROM " . $table . " WHERE id = ? AND subject_id = ?");
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
        $this->materialModel->deleteMaterial($id, $courseId);
        
        $redirectUrl = $_SERVER['HTTP_REFERER'] ?? '/course?id=' . $courseId;
        $this->redirect($redirectUrl);
    }

    public function deleteAssignment(Request $request, Response $response): void {
        $courseId = (int)$request->post('subject_id');
        if (!$this->verifyTeacherAccess($courseId)) $this->redirect('/course?id=' . $courseId);
        $id = (int)$request->post('id');
        
        $this->deletePhysicalFile('assignments', $id, $courseId);
        $this->materialModel->deleteAssignment($id, $courseId);
        
        $redirectUrl = $_SERVER['HTTP_REFERER'] ?? '/course?id=' . $courseId;
        $this->redirect($redirectUrl);
    }

    public function deleteExam(Request $request, Response $response): void {
        $courseId = (int)$request->post('subject_id');
        if (!$this->verifyTeacherAccess($courseId)) $this->redirect('/course?id=' . $courseId);
        $id = (int)$request->post('id');
        
        $this->deletePhysicalFile('exams', $id, $courseId);
        $this->materialModel->deleteExam($id, $courseId);
        
        $redirectUrl = $_SERVER['HTTP_REFERER'] ?? '/course?id=' . $courseId;
        $this->redirect($redirectUrl);
    }

    public function deleteQuiz(Request $request, Response $response): void {
        $courseId = (int)$request->post('subject_id');
        if (!$this->verifyTeacherAccess($courseId)) $this->redirect('/course?id=' . $courseId);
        $id = (int)$request->post('id');
        
        $this->deletePhysicalFile('quizzes', $id, $courseId);
        $this->materialModel->deleteQuiz($id, $courseId);
        
        $redirectUrl = $_SERVER['HTTP_REFERER'] ?? '/course?id=' . $courseId;
        $this->redirect($redirectUrl);
    }

    public function showAttendance(Request $request, Response $response): void {
        $courseId = (int)$request->get('id');
        if (!$this->verifyTeacherAccess($courseId) && !$this->verifyStudentEnrollment($courseId)) {
            $this->redirect('/dashboard');
        }

        $course = $this->courseModel->getById($courseId);
        if (!$course) $this->redirect('/dashboard');

        $user = $this->userModel->findById($_SESSION['user_id']);

        $data = [
            'userRole' => $_SESSION['user_role'],
            'userName' => $_SESSION['user_name'],
            'course' => $course,
            'userAvatar' => $user['avatar_path'] ?? null
        ];

        if ($_SESSION['user_role'] === 'teacher') {
            $data['students'] = $this->enrollmentModel->getEnrolledStudents($courseId);
            $data['attendance_dates'] = $this->attendanceModel->getRecentDates($courseId);
            
            $data['attendance_matrix'] = [];
            if (!empty($data['attendance_dates'])) {
                $minDate = end($data['attendance_dates']);
                $maxDate = $data['attendance_dates'][0];
                $data['attendance_matrix'] = $this->attendanceModel->getMatrix($courseId, $minDate, $maxDate);
            }
        } else {
            $data['attendance_log'] = $this->attendanceModel->getStudentLog($courseId, $_SESSION['user_id']);

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

        $course = $this->courseModel->getById($courseId);
        if (!$course) $this->redirect('/dashboard');

        $user = $this->userModel->findById($_SESSION['user_id']);

        $data = [
            'userRole' => $_SESSION['user_role'],
            'userName' => $_SESSION['user_name'],
            'course' => $course,
            'userAvatar' => $user['avatar_path'] ?? null,
            'enrolled_students' => $this->enrollmentModel->getEnrolledStudents($courseId)
        ];

        $this->render('take-attendance', $data);
    }

    public function markAttendance(Request $request, Response $response): void {
        $courseId = (int)$request->post('subject_id');
        if (!$this->verifyTeacherAccess($courseId)) $this->redirect('/dashboard');

        $date = $request->post('attendance_date');
        $attendanceData = $request->post('attendance') ?? [];

        if (!empty($date) && !empty($attendanceData)) {
            foreach ($attendanceData as $studentId => $status) {
                $studentId = (int)$studentId;
                if (!in_array($status, ['present', 'late', 'absent'])) continue;
                $this->attendanceModel->upsertRecord($courseId, $studentId, $date, $status);
            }
        }

        $this->redirect('/course/attendance?id=' . $courseId);
    }

    public function manageEnrollments(Request $request, Response $response): void {
        $courseId = (int)$request->get('id');
        if (!$courseId || $_SESSION['user_role'] !== 'teacher' || !$this->verifyTeacherAccess($courseId)) {
            $this->redirect('/dashboard');
        }

        $course = $this->courseModel->getById($courseId);
        if (!$course) {
            $this->redirect('/dashboard');
        }

        $user = $this->userModel->findById($_SESSION['user_id']);

        $data = [
            'userRole' => $_SESSION['user_role'],
            'userName' => $_SESSION['user_name'],
            'userEmail' => $_SESSION['user_email'],
            'userAvatar' => $user['avatar_path'] ?? null,
            'course' => $course,
            'enrolledStudents' => $this->enrollmentModel->getEnrolledStudents($courseId),
            'allStudents' => $this->userModel->getByRole('student')
        ];

        $this->render('course-enrollments', $data);
    }
}