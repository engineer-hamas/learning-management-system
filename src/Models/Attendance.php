<?php
namespace App\Models;

use App\Core\Model;

class Attendance extends Model {
    
    /**
     * Get recent distinct attendance dates for a course
     */
    public function getRecentDates(int $courseId, int $limit = 15): array {
        $stmt = $this->db->prepare("SELECT DISTINCT date FROM attendance WHERE subject_id = ? ORDER BY date DESC LIMIT ?");
        $stmt->bind_param("ii", $courseId, $limit);
        $stmt->execute();
        $dates = [];
        $res = $stmt->get_result();
        while($r = $res->fetch_assoc()) {
            $dates[] = $r['date'];
        }
        $stmt->close();
        return $dates;
    }

    /**
     * Get attendance matrix for all students between two dates
     */
    public function getMatrix(int $courseId, string $minDate, string $maxDate): array {
        $matrix = [];
        $stmt = $this->db->prepare("SELECT student_id, date, status FROM attendance WHERE subject_id = ? AND date >= ? AND date <= ?");
        $stmt->bind_param("iss", $courseId, $minDate, $maxDate);
        $stmt->execute();
        $res = $stmt->get_result();
        while($r = $res->fetch_assoc()) {
            $matrix[$r['student_id']][$r['date']] = $r['status'];
        }
        $stmt->close();
        return $matrix;
    }

    /**
     * Get a specific student's attendance log for a course
     */
    public function getStudentLog(int $courseId, int $studentId): array {
        $stmt = $this->db->prepare("SELECT date, status FROM attendance WHERE subject_id = ? AND student_id = ? ORDER BY date DESC");
        $stmt->bind_param("ii", $courseId, $studentId);
        $stmt->execute();
        $log = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $log;
    }

    /**
     * Upsert an attendance record
     */
    public function upsertRecord(int $courseId, int $studentId, string $date, string $status): bool {
        // Check if record exists
        $checkStmt = $this->db->prepare("SELECT id FROM attendance WHERE subject_id = ? AND student_id = ? AND date = ?");
        $checkStmt->bind_param("iis", $courseId, $studentId, $date);
        $checkStmt->execute();
        $res = $checkStmt->get_result();
        
        $success = false;
        
        if ($row = $res->fetch_assoc()) {
            // Update
            $updateStmt = $this->db->prepare("UPDATE attendance SET status = ? WHERE id = ?");
            $updateStmt->bind_param("si", $status, $row['id']);
            $success = $updateStmt->execute();
            $updateStmt->close();
        } else {
            // Insert
            $insertStmt = $this->db->prepare("INSERT INTO attendance (subject_id, student_id, date, status) VALUES (?, ?, ?, ?)");
            $insertStmt->bind_param("iiss", $courseId, $studentId, $date, $status);
            $success = $insertStmt->execute();
            $insertStmt->close();
        }
        $checkStmt->close();
        
        return $success;
    }
}
