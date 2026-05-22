<?php
namespace App\Core;

use mysqli;

/**
 * Class Database
 * 
 * Manages MariaDB server connection and auto-schema generation (Singleton).
 */
class Database {
    private static ?mysqli $connection = null;

    /**
     * Obtain the singleton database connection
     * 
     * @return mysqli
     */
    public static function getConnection(): mysqli {
        if (self::$connection === null) {
            self::initialize();
        }
        return self::$connection;
    }

    /**
     * Establish server connection, create database, and initialize schema tables
     */
    private static function initialize(): void {
        // 1. First, connect to the database server without selecting a database
        $conn = @new mysqli(DB_HOST, DB_USER, DB_PASS);

        if ($conn->connect_error) {
            die("Database Server Connection Failed: " . $conn->connect_error);
        }

        // 2. Create the 'lms' database if it doesn't exist
        $dbName = $conn->real_escape_string(DB_NAME);
        $createDbQuery = "CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
        if (!$conn->query($createDbQuery)) {
            die("Error creating database: " . $conn->error);
        }

        // 3. Select the database
        if (!$conn->select_db(DB_NAME)) {
            die("Error selecting database: " . $conn->error);
        }

        // 4. Create departments table if it doesn't exist
        $createDepartmentsTable = "
        CREATE TABLE IF NOT EXISTS `departments` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(100) NOT NULL UNIQUE,
            `code` VARCHAR(10) NOT NULL UNIQUE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
        if (!$conn->query($createDepartmentsTable)) {
            die("Error creating departments table: " . $conn->error);
        }

        // 5. Create subjects table if it doesn't exist
        $createSubjectsTable = "
        CREATE TABLE IF NOT EXISTS `subjects` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `department_id` INT NOT NULL,
            `name` VARCHAR(100) NOT NULL,
            `code` VARCHAR(20) NOT NULL UNIQUE,
            `credits` INT DEFAULT 3,
            FOREIGN KEY (`department_id`) REFERENCES `departments`(`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
        if (!$conn->query($createSubjectsTable)) {
            die("Error creating subjects table: " . $conn->error);
        }

        // 6. Create the 'users' table if it doesn't exist (base structure)
        $createUsersTable = "
        CREATE TABLE IF NOT EXISTS `users` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(100) NOT NULL,
            `email` VARCHAR(100) NOT NULL UNIQUE,
            `password` VARCHAR(255) NOT NULL,
            `role` ENUM('student', 'teacher') NOT NULL DEFAULT 'student',
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
        if (!$conn->query($createUsersTable)) {
            die("Error creating users table: " . $conn->error);
        }

        // 7. Safe Alterations: Dynamically add missing columns to 'users' table if they don't exist
        $columnsToCheck = [
            'avatar_path' => "VARCHAR(255) DEFAULT NULL AFTER `role`",
            'phone' => "VARCHAR(20) DEFAULT NULL AFTER `avatar_path`",
            'bio' => "TEXT DEFAULT NULL AFTER `phone`",
            'department_id' => "INT DEFAULT NULL AFTER `bio`",
            'title' => "VARCHAR(100) DEFAULT NULL AFTER `department_id`",
            'office_hours' => "VARCHAR(255) DEFAULT NULL AFTER `title`",
            'specialization' => "VARCHAR(255) DEFAULT NULL AFTER `office_hours`",
            'student_id' => "VARCHAR(50) DEFAULT NULL AFTER `specialization`",
            'expected_graduation' => "VARCHAR(50) DEFAULT NULL AFTER `student_id`",
            'study_style' => "VARCHAR(50) DEFAULT NULL AFTER `expected_graduation`",
            'notify_email' => "TINYINT DEFAULT 1 AFTER `study_style`",
            'notify_class_alerts' => "TINYINT DEFAULT 1 AFTER `notify_email`",
            'notify_marketing' => "TINYINT DEFAULT 0 AFTER `notify_class_alerts`"
        ];

        foreach ($columnsToCheck as $colName => $definition) {
            $colCheck = $conn->query("SHOW COLUMNS FROM `users` LIKE '{$colName}'");
            if ($colCheck && $colCheck->num_rows === 0) {
                if (!$conn->query("ALTER TABLE `users` ADD COLUMN `{$colName}` {$definition}")) {
                    die("Error altering users table for column '{$colName}': " . $conn->error);
                }
            }
        }

        // 8. Safely add foreign key constraint if it doesn't exist
        $fkCheck = $conn->query("
            SELECT * FROM information_schema.TABLE_CONSTRAINTS 
            WHERE CONSTRAINT_SCHEMA = '" . DB_NAME . "' 
            AND TABLE_NAME = 'users' 
            AND CONSTRAINT_NAME = 'fk_users_department'
        ");
        if ($fkCheck && $fkCheck->num_rows === 0) {
            if (!$conn->query("ALTER TABLE `users` ADD CONSTRAINT `fk_users_department` FOREIGN KEY (`department_id`) REFERENCES `departments`(`id`) ON DELETE SET NULL")) {
                die("Error adding foreign key constraint to users table: " . $conn->error);
            }
        }

        // 9. Create teacher_subjects table if it doesn't exist
        $createTeacherSubjectsTable = "
        CREATE TABLE IF NOT EXISTS `teacher_subjects` (
            `teacher_id` INT NOT NULL,
            `subject_id` INT NOT NULL,
            PRIMARY KEY (`teacher_id`, `subject_id`),
            FOREIGN KEY (`teacher_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
            FOREIGN KEY (`subject_id`) REFERENCES `subjects`(`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
        if (!$conn->query($createTeacherSubjectsTable)) {
            die("Error creating teacher_subjects table: " . $conn->error);
        }

        // 10. Create student_subjects table if it doesn't exist
        $createStudentSubjectsTable = "
        CREATE TABLE IF NOT EXISTS `student_subjects` (
            `student_id` INT NOT NULL,
            `subject_id` INT NOT NULL,
            PRIMARY KEY (`student_id`, `subject_id`),
            FOREIGN KEY (`student_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
            FOREIGN KEY (`subject_id`) REFERENCES `subjects`(`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
        if (!$conn->query($createStudentSubjectsTable)) {
            die("Error creating student_subjects table: " . $conn->error);
        }

        // 11. Seeding: Seed default departments if table is empty
        $depCount = $conn->query("SELECT COUNT(*) as count FROM `departments`")->fetch_assoc()['count'];
        if ($depCount == 0) {
            $conn->query("
                INSERT INTO `departments` (`name`, `code`) VALUES 
                ('Computer Science', 'CS'),
                ('Mathematics', 'MATH'),
                ('Physics', 'PHYS'),
                ('Business Administration', 'BUS'),
                ('English Literature', 'ENG')
            ");
        }

        // 12. Seeding: Seed default subjects if table is empty
        $subjCount = $conn->query("SELECT COUNT(*) as count FROM `subjects`")->fetch_assoc()['count'];
        if ($subjCount == 0) {
            $deps = [];
            $res = $conn->query("SELECT `id`, `code` FROM `departments`");
            while ($row = $res->fetch_assoc()) {
                $deps[$row['code']] = $row['id'];
            }

            if (!empty($deps)) {
                $stmt = $conn->prepare("INSERT INTO `subjects` (`department_id`, `name`, `code`, `credits`) VALUES (?, ?, ?, ?)");
                
                $courses = [
                    ['CS', 'Introduction to Programming', 'CS101', 4],
                    ['CS', 'Data Structures & Algorithms', 'CS202', 4],
                    ['CS', 'Database Management Systems', 'CS303', 3],
                    ['CS', 'Software Engineering', 'CS401', 3],
                    ['MATH', 'Calculus I', 'MATH101', 4],
                    ['MATH', 'Linear Algebra', 'MATH201', 3],
                    ['MATH', 'Discrete Mathematics', 'MATH202', 3],
                    ['PHYS', 'General Physics I', 'PHYS101', 4],
                    ['PHYS', 'Quantum Mechanics', 'PHYS301', 3],
                    ['BUS', 'Introduction to Business', 'BUS101', 3],
                    ['BUS', 'Principles of Marketing', 'BUS201', 3],
                    ['ENG', 'Academic Writing', 'ENG101', 3],
                    ['ENG', 'Shakespeare Studies', 'ENG302', 3]
                ];

                foreach ($courses as $c) {
                    $depCode = $c[0];
                    if (isset($deps[$depCode])) {
                        $depId = $deps[$depCode];
                        $name = $c[1];
                        $code = $c[2];
                        $credits = $c[3];
                        $stmt->bind_param("issi", $depId, $name, $code, $credits);
                        $stmt->execute();
                    }
                }
                $stmt->close();
            }
        }

        // 13. Dynamic Directory Creation for Avatars Upload
        $uploadDir = dirname(dirname(__DIR__)) . '/public/uploads/avatars';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        self::$connection = $conn;
    }
}
Class_Alias('App\\Core\\Database', 'Database');
