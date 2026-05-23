<?php
namespace App\Core\Database;

use mysqli;

/**
 * Class SchemaMigration
 * 
 * Handles schema creation, separating role-specific columns into dedicated tables (CTI pattern).
 */
class SchemaMigration {
    public static function migrate(mysqli $conn): void {
        // Departments
        $conn->query("
            CREATE TABLE IF NOT EXISTS `departments` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `name` VARCHAR(100) NOT NULL UNIQUE,
                `code` VARCHAR(10) NOT NULL UNIQUE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // Subjects
        $conn->query("
            CREATE TABLE IF NOT EXISTS `subjects` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `department_id` INT NOT NULL,
                `name` VARCHAR(100) NOT NULL,
                `code` VARCHAR(20) NOT NULL UNIQUE,
                `description` TEXT DEFAULT NULL,
                `credits` INT DEFAULT 3,
                FOREIGN KEY (`department_id`) REFERENCES `departments`(`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // Users (Generic Info Only)
        $conn->query("
            CREATE TABLE IF NOT EXISTS `users` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `name` VARCHAR(100) NOT NULL,
                `email` VARCHAR(100) NOT NULL UNIQUE,
                `password` VARCHAR(255) NOT NULL,
                `role` ENUM('student', 'teacher') NOT NULL DEFAULT 'student',
                `avatar_path` VARCHAR(255) DEFAULT NULL,
                `phone` VARCHAR(20) DEFAULT NULL,
                `bio` TEXT DEFAULT NULL,
                `notify_email` TINYINT DEFAULT 1,
                `notify_class_alerts` TINYINT DEFAULT 1,
                `notify_marketing` TINYINT DEFAULT 0,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // Teachers table (Role Specific)
        $conn->query("
            CREATE TABLE IF NOT EXISTS `teachers` (
                `user_id` INT NOT NULL PRIMARY KEY,
                `department_id` INT DEFAULT NULL,
                `title` VARCHAR(100) DEFAULT NULL,
                `office_hours` VARCHAR(255) DEFAULT NULL,
                `specialization` VARCHAR(255) DEFAULT NULL,
                FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
                FOREIGN KEY (`department_id`) REFERENCES `departments`(`id`) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // Students table (Role Specific)
        $conn->query("
            CREATE TABLE IF NOT EXISTS `students` (
                `user_id` INT NOT NULL PRIMARY KEY,
                `department_id` INT DEFAULT NULL,
                `student_id` VARCHAR(50) DEFAULT NULL,
                `expected_graduation` VARCHAR(50) DEFAULT NULL,
                `study_style` VARCHAR(50) DEFAULT NULL,
                FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
                FOREIGN KEY (`department_id`) REFERENCES `departments`(`id`) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // Mapping tables
        $conn->query("
            CREATE TABLE IF NOT EXISTS `teacher_subjects` (
                `teacher_id` INT NOT NULL,
                `subject_id` INT NOT NULL,
                PRIMARY KEY (`teacher_id`, `subject_id`),
                FOREIGN KEY (`teacher_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
                FOREIGN KEY (`subject_id`) REFERENCES `subjects`(`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        $conn->query("
            CREATE TABLE IF NOT EXISTS `enrollments` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `student_id` INT NOT NULL,
                `subject_id` INT NOT NULL,
                `enrolled_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY `unique_enrollment` (`student_id`, `subject_id`),
                FOREIGN KEY (`student_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
                FOREIGN KEY (`subject_id`) REFERENCES `subjects`(`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        $conn->query("
            CREATE TABLE IF NOT EXISTS `course_materials` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `subject_id` INT NOT NULL,
                `teacher_id` INT NOT NULL,
                `title` VARCHAR(255) NOT NULL,
                `file_path` VARCHAR(255) NOT NULL,
                `original_filename` VARCHAR(255) DEFAULT NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (`subject_id`) REFERENCES `subjects`(`id`) ON DELETE CASCADE,
                FOREIGN KEY (`teacher_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        $conn->query("
            CREATE TABLE IF NOT EXISTS `assignments` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `subject_id` INT NOT NULL,
                `teacher_id` INT NOT NULL,
                `title` VARCHAR(255) NOT NULL,
                `description` TEXT,
                `due_date` DATETIME DEFAULT NULL,
                `file_path` VARCHAR(255) DEFAULT NULL,
                `original_filename` VARCHAR(255) DEFAULT NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (`subject_id`) REFERENCES `subjects`(`id`) ON DELETE CASCADE,
                FOREIGN KEY (`teacher_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        $conn->query("
            CREATE TABLE IF NOT EXISTS `exams` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `subject_id` INT NOT NULL,
                `teacher_id` INT NOT NULL,
                `title` VARCHAR(255) NOT NULL,
                `exam_date` DATETIME NOT NULL,
                `duration_minutes` INT DEFAULT 60,
                `file_path` VARCHAR(255) DEFAULT NULL,
                `original_filename` VARCHAR(255) DEFAULT NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (`subject_id`) REFERENCES `subjects`(`id`) ON DELETE CASCADE,
                FOREIGN KEY (`teacher_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        $conn->query("
            CREATE TABLE IF NOT EXISTS `quizzes` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `subject_id` INT NOT NULL,
                `teacher_id` INT NOT NULL,
                `title` VARCHAR(255) NOT NULL,
                `description` TEXT,
                `file_path` VARCHAR(255) DEFAULT NULL,
                `original_filename` VARCHAR(255) DEFAULT NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (`subject_id`) REFERENCES `subjects`(`id`) ON DELETE CASCADE,
                FOREIGN KEY (`teacher_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // Create upload directory
        $uploadDir = dirname(dirname(dirname(__DIR__))) . '/public/uploads/avatars';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
    }
}
