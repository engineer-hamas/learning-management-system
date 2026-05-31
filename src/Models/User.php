<?php
namespace App\Models;

use App\Core\Model;

class User extends Model {
    
    /**
     * Check if a user with the given email exists
     */
    public function emailExists(string $email): bool {
        $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        $exists = $stmt->num_rows > 0;
        $stmt->close();
        return $exists;
    }

    /**
     * Fetch user by email
     */
    public function findByEmail(string $email): ?array {
        $stmt = $this->db->prepare("SELECT id, name, email, password, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc() ?: null;
        $stmt->close();
        return $user;
    }

    /**
     * Fetch user by ID
     */
    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT id, name, email, role, avatar_path FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc() ?: null;
        $stmt->close();
        return $user;
    }

    /**
     * Create a new user account along with their role-specific profile
     */
    public function create(string $name, string $email, string $password, string $role): int {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $this->db->begin_transaction();
        
        try {
            $stmt = $this->db->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $email, $hashedPassword, $role);
            $stmt->execute();
            
            $newUserId = $this->db->insert_id;
            $stmt->close();
            
            if ($role === 'teacher') {
                $tStmt = $this->db->prepare("INSERT INTO teachers (user_id) VALUES (?)");
                $tStmt->bind_param("i", $newUserId);
                $tStmt->execute();
                $tStmt->close();
            } elseif ($role === 'student') {
                $sStmt = $this->db->prepare("INSERT INTO students (user_id) VALUES (?)");
                $sStmt->bind_param("i", $newUserId);
                $sStmt->execute();
                $sStmt->close();
            }
            
            $this->db->commit();
            return $newUserId;
        } catch (\Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    /**
     * Get complete profile details for a user
     */
    public function getFullProfile(int $id): ?array {
        $stmt = $this->db->prepare("
            SELECT u.id, u.name, u.email, u.role, u.avatar_path, u.phone, u.bio, 
                   u.notify_email, u.notify_class_alerts, u.notify_marketing,
                   t.department_id as t_dept, t.title, t.office_hours, t.specialization,
                   s.department_id as s_dept, s.student_id, s.expected_graduation, s.study_style
            FROM users u
            LEFT JOIN teachers t ON u.id = t.user_id
            LEFT JOIN students s ON u.id = s.user_id
            WHERE u.id = ?
        ");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc() ?: null;
        $stmt->close();
        
        if ($user) {
            $user['department_id'] = $user['role'] === 'teacher' ? $user['t_dept'] : $user['s_dept'];
        }
        
        return $user;
    }

    /**
     * Update user avatar
     */
    public function updateAvatar(int $id, string $avatarPath): bool {
        $stmt = $this->db->prepare("UPDATE users SET avatar_path = ? WHERE id = ?");
        $stmt->bind_param("si", $avatarPath, $id);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    /**
     * Count total users by role
     */
    public function countByRole(string $role): int {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM users WHERE role = ?");
        $stmt->bind_param("s", $role);
        $stmt->execute();
        $count = $stmt->get_result()->fetch_assoc()['count'] ?? 0;
        $stmt->close();
        return (int)$count;
    }

    /**
     * Fetch all users of a specific role
     */
    public function getByRole(string $role): array {
        $stmt = $this->db->prepare("SELECT id, name, email FROM users WHERE role = ? ORDER BY name ASC");
        $stmt->bind_param("s", $role);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $result;
    }

    public function updateGeneralProfile(int $id, string $name, string $email, string $phone, string $bio, ?string $avatarPath = null): bool {
        if ($avatarPath !== null) {
            $stmt = $this->db->prepare("UPDATE users SET name = ?, email = ?, phone = ?, bio = ?, avatar_path = ? WHERE id = ?");
            $stmt->bind_param("sssssi", $name, $email, $phone, $bio, $avatarPath, $id);
        } else {
            $stmt = $this->db->prepare("UPDATE users SET name = ?, email = ?, phone = ?, bio = ? WHERE id = ?");
            $stmt->bind_param("ssssi", $name, $email, $phone, $bio, $id);
        }
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    public function updateTeacherProfile(int $userId, ?int $deptId, string $title, string $officeHours, string $specialization): bool {
        $stmt = $this->db->prepare("UPDATE teachers SET department_id = ?, title = ?, office_hours = ?, specialization = ? WHERE user_id = ?");
        $stmt->bind_param("isssi", $deptId, $title, $officeHours, $specialization, $userId);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    public function updateStudentProfile(int $userId, ?int $deptId, string $studentId, string $expectedGraduation, string $studyStyle): bool {
        $stmt = $this->db->prepare("UPDATE students SET department_id = ?, student_id = ?, expected_graduation = ?, study_style = ? WHERE user_id = ?");
        $stmt->bind_param("isssi", $deptId, $studentId, $expectedGraduation, $studyStyle, $userId);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    public function updatePassword(int $userId, string $newPasswordHash): bool {
        $stmt = $this->db->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $newPasswordHash, $userId);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    public function updateNotificationSettings(int $userId, int $emailNotify, int $classAlerts, int $marketing): bool {
        $stmt = $this->db->prepare("UPDATE users SET notify_email = ?, notify_class_alerts = ?, notify_marketing = ? WHERE id = ?");
        $stmt->bind_param("iiii", $emailNotify, $classAlerts, $marketing, $userId);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }
}
