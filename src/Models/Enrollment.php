<?php
namespace App\Models;

use App\Core\Model;

class Enrollment extends Model {
    
    /**
     * Get all students enrolled in a specific course
     */
    public function getEnrolledStudents(int $courseId): array {
        $stmt = $this->db->prepare("
            SELECT u.id, u.name, u.email, e.enrolled_at, s.student_id as roll_no
            FROM users u
            JOIN enrollments e ON u.id = e.student_id
            LEFT JOIN students s ON u.id = s.user_id
            WHERE e.subject_id = ?
            ORDER BY u.name ASC
        ");
        $stmt->bind_param("i", $courseId);
        $stmt->execute();
        $students = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $students;
    }

    /**
     * Check if a student is already enrolled in a course
     */
    public function isEnrolled(int $studentId, int $courseId): bool {
        $stmt = $this->db->prepare("SELECT 1 FROM enrollments WHERE student_id = ? AND subject_id = ?");
        $stmt->bind_param("ii", $studentId, $courseId);
        $stmt->execute();
        $isEnrolled = $stmt->get_result()->num_rows > 0;
        $stmt->close();
        return $isEnrolled;
    }

    /**
     * Enroll a student in a course
     */
    public function enrollStudent(int $studentId, int $courseId): bool {
        if ($this->isEnrolled($studentId, $courseId)) {
            return false;
        }
        
        $stmt = $this->db->prepare("INSERT INTO enrollments (student_id, subject_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $studentId, $courseId);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    /**
     * Unenroll a student from a course
     */
    public function unenrollStudent(int $studentId, int $courseId): bool {
        $stmt = $this->db->prepare("DELETE FROM enrollments WHERE student_id = ? AND subject_id = ?");
        $stmt->bind_param("ii", $studentId, $courseId);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    /**
     * Count total students enrolled across all courses taught by a teacher
     */
    public function countTotalStudentsByTeacher(int $teacherId): int {
        $stmt = $this->db->prepare("
            SELECT COUNT(DISTINCT e.student_id) as count 
            FROM enrollments e 
            JOIN teacher_subjects ts ON e.subject_id = ts.subject_id 
            WHERE ts.teacher_id = ?
        ");
        $stmt->bind_param("i", $teacherId);
        $stmt->execute();
        $count = $stmt->get_result()->fetch_assoc()['count'] ?? 0;
        $stmt->close();
        return (int)$count;
    }
}
