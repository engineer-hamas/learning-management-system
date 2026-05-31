<?php
namespace App\Models;

use App\Core\Model;

class Course extends Model {
    
    /**
     * Get basic course details by ID
     */
    public function getById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT id, department_id, name, code, description, credits FROM subjects WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $course = $stmt->get_result()->fetch_assoc() ?: null;
        $stmt->close();
        return $course;
    }

    /**
     * Count total active courses
     */
    public function countTotal(): int {
        $result = $this->db->query("SELECT COUNT(*) as count FROM subjects");
        return (int)($result->fetch_assoc()['count'] ?? 0);
    }

    /**
     * Get all courses
     */
    public function getAll(): array {
        $result = $this->db->query("SELECT id, department_id, name, code, credits FROM subjects ORDER BY code ASC");
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Get courses a student is enrolled in
     */
    public function getEnrolledCourses(int $studentId): array {
        $stmt = $this->db->prepare("
            SELECT s.id, s.name, s.code, s.description, e.enrolled_at 
            FROM subjects s 
            JOIN enrollments e ON s.id = e.subject_id 
            WHERE e.student_id = ? 
            ORDER BY s.name ASC
        ");
        $stmt->bind_param("i", $studentId);
        $stmt->execute();
        $courses = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $courses;
    }

    /**
     * Get courses a teacher is assigned to
     */
    public function getTaughtCourses(int $teacherId): array {
        $stmt = $this->db->prepare("
            SELECT s.id, s.name, s.code, s.description 
            FROM subjects s 
            JOIN teacher_subjects ts ON s.id = ts.subject_id 
            WHERE ts.teacher_id = ? 
            ORDER BY s.name ASC
        ");
        $stmt->bind_param("i", $teacherId);
        $stmt->execute();
        $courses = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $courses;
    }

    /**
     * Verify if a teacher has access to a specific course
     */
    public function verifyTeacherAccess(int $teacherId, int $courseId): bool {
        $stmt = $this->db->prepare("SELECT 1 FROM teacher_subjects WHERE teacher_id = ? AND subject_id = ?");
        $stmt->bind_param("ii", $teacherId, $courseId);
        $stmt->execute();
        $hasAccess = $stmt->get_result()->num_rows > 0;
        $stmt->close();
        return $hasAccess;
    }

    /**
     * Verify if a student is enrolled in a specific course
     */
    public function verifyStudentEnrollment(int $studentId, int $courseId): bool {
        $stmt = $this->db->prepare("SELECT 1 FROM enrollments WHERE student_id = ? AND subject_id = ?");
        $stmt->bind_param("ii", $studentId, $courseId);
        $stmt->execute();
        $hasAccess = $stmt->get_result()->num_rows > 0;
        $stmt->close();
        return $hasAccess;
    }

    /**
     * Create a new course (subject)
     */
    public function create(int $departmentId, string $name, string $code, string $description, int $credits): int {
        $stmt = $this->db->prepare("INSERT INTO subjects (department_id, name, code, description, credits) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("isssi", $departmentId, $name, $code, $description, $credits);
        $stmt->execute();
        $newCourseId = $this->db->insert_id;
        $stmt->close();
        return $newCourseId;
    }
}
