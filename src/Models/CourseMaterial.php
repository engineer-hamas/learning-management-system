<?php
namespace App\Models;

use App\Core\Model;

class CourseMaterial extends Model {
    
    // ==========================================
    // Assignments
    // ==========================================
    
    public function getAssignments(int $courseId): array {
        $stmt = $this->db->prepare("SELECT id, title, description, due_date, file_path, original_filename FROM assignments WHERE subject_id = ? ORDER BY due_date ASC");
        $stmt->bind_param("i", $courseId);
        $stmt->execute();
        $items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $items;
    }
    
    public function addAssignment(int $courseId, int $teacherId, string $title, string $desc, ?string $dueDate, ?string $filePath, ?string $originalName): bool {
        $stmt = $this->db->prepare("INSERT INTO assignments (subject_id, teacher_id, title, description, due_date, file_path, original_filename) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iisssss", $courseId, $teacherId, $title, $desc, $dueDate, $filePath, $originalName);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }
    
    public function deleteAssignment(int $id, int $courseId): bool {
        $stmt = $this->db->prepare("DELETE FROM assignments WHERE id = ? AND subject_id = ?");
        $stmt->bind_param("ii", $id, $courseId);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    // ==========================================
    // Exams
    // ==========================================
    
    public function getExams(int $courseId): array {
        $stmt = $this->db->prepare("SELECT id, title, exam_date, duration_minutes, file_path, original_filename FROM exams WHERE subject_id = ? ORDER BY exam_date ASC");
        $stmt->bind_param("i", $courseId);
        $stmt->execute();
        $items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $items;
    }

    public function addExam(int $courseId, int $teacherId, string $title, string $examDate, int $duration, ?string $filePath, ?string $originalName): bool {
        $stmt = $this->db->prepare("INSERT INTO exams (subject_id, teacher_id, title, exam_date, duration_minutes, file_path, original_filename) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iississ", $courseId, $teacherId, $title, $examDate, $duration, $filePath, $originalName);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    public function deleteExam(int $id, int $courseId): bool {
        $stmt = $this->db->prepare("DELETE FROM exams WHERE id = ? AND subject_id = ?");
        $stmt->bind_param("ii", $id, $courseId);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    // ==========================================
    // Quizzes
    // ==========================================

    public function getQuizzes(int $courseId): array {
        $stmt = $this->db->prepare("SELECT id, title, description, file_path, original_filename, created_at FROM quizzes WHERE subject_id = ? ORDER BY created_at DESC");
        $stmt->bind_param("i", $courseId);
        $stmt->execute();
        $items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $items;
    }

    public function addQuiz(int $courseId, int $teacherId, string $title, string $desc, ?string $filePath, ?string $originalName): bool {
        $stmt = $this->db->prepare("INSERT INTO quizzes (subject_id, teacher_id, title, description, file_path, original_filename) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iissss", $courseId, $teacherId, $title, $desc, $filePath, $originalName);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    public function deleteQuiz(int $id, int $courseId): bool {
        $stmt = $this->db->prepare("DELETE FROM quizzes WHERE id = ? AND subject_id = ?");
        $stmt->bind_param("ii", $id, $courseId);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    // ==========================================
    // General Materials
    // ==========================================

    public function getMaterials(int $courseId): array {
        $stmt = $this->db->prepare("SELECT id, title, file_path, original_filename, created_at FROM course_materials WHERE subject_id = ? ORDER BY created_at DESC");
        $stmt->bind_param("i", $courseId);
        $stmt->execute();
        $items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $items;
    }

    public function addMaterial(int $courseId, int $teacherId, string $title, ?string $filePath, ?string $originalName): bool {
        $stmt = $this->db->prepare("INSERT INTO course_materials (subject_id, teacher_id, title, file_path, original_filename) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iisss", $courseId, $teacherId, $title, $filePath, $originalName);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    public function deleteMaterial(int $id, int $courseId): bool {
        $stmt = $this->db->prepare("DELETE FROM course_materials WHERE id = ? AND subject_id = ?");
        $stmt->bind_param("ii", $id, $courseId);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    // ==========================================
    // Aggregations
    // ==========================================

    /**
     * Get recent materials across all courses a student is enrolled in
     */
    public function getRecentForStudent(int $studentId, int $limit = 5): array {
        $stmt = $this->db->prepare("
            SELECT cm.id, cm.title, cm.file_path, cm.created_at, s.name as course_name, 'Material' as type 
            FROM course_materials cm
            JOIN enrollments e ON cm.subject_id = e.subject_id
            JOIN subjects s ON cm.subject_id = s.id
            WHERE e.student_id = ?
            ORDER BY cm.created_at DESC
            LIMIT ?
        ");
        $stmt->bind_param("ii", $studentId, $limit);
        $stmt->execute();
        $items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $items;
    }

    /**
     * Get recent materials uploaded by a teacher across all their courses
     */
    public function getRecentByTeacher(int $teacherId, int $limit = 5): array {
        $stmt = $this->db->prepare("
            SELECT cm.id, cm.title, cm.file_path, cm.created_at, s.name as course_name, 'Material' as type 
            FROM course_materials cm
            JOIN teacher_subjects ts ON cm.subject_id = ts.subject_id
            JOIN subjects s ON cm.subject_id = s.id
            WHERE ts.teacher_id = ?
            ORDER BY cm.created_at DESC
            LIMIT ?
        ");
        $stmt->bind_param("ii", $teacherId, $limit);
        $stmt->execute();
        $items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $items;
    }
}
