<?php
namespace App\Core\Database;

use mysqli;

/**
 * Class Seeder
 * 
 * Handles initial data population.
 */
class Seeder {
    public static function seed(mysqli $conn): void {
        // Departments
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

        // Subjects
        $subjCount = $conn->query("SELECT COUNT(*) as count FROM `subjects`")->fetch_assoc()['count'];
        if ($subjCount == 0) {
            $deps = [];
            $res = $conn->query("SELECT `id`, `code` FROM `departments`");
            if ($res) {
                while ($row = $res->fetch_assoc()) {
                    $deps[$row['code']] = $row['id'];
                }
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

        // Users
        $userCount = $conn->query("SELECT COUNT(*) as count FROM `users`")->fetch_assoc()['count'];
        if ($userCount == 0) {
            $hashedPassword = password_hash('password', PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO `users` (`name`, `email`, `password`, `role`) VALUES (?, ?, ?, ?)");
            
            $users = [
                ['Admin User', 'admin@example.com', 'admin'],
                ['Teacher One', 'teacher1@example.com', 'teacher'],
                ['Teacher Two', 'teacher2@example.com', 'teacher'],
                ['Student One', 'student1@example.com', 'student'],
                ['Student Two', 'student2@example.com', 'student'],
                ['Student Three', 'student3@example.com', 'student']
            ];

            $name = '';
            $email = '';
            $role = '';
            $stmt->bind_param("ssss", $name, $email, $hashedPassword, $role);

            foreach ($users as $u) {
                $name = $u[0];
                $email = $u[1];
                $role = $u[2];
                $stmt->execute();
                
                $userId = $stmt->insert_id;
                
                if ($role === 'teacher') {
                    $conn->query("INSERT INTO `teachers` (`user_id`) VALUES ($userId)");
                } elseif ($role === 'student') {
                    $conn->query("INSERT INTO `students` (`user_id`) VALUES ($userId)");
                }
            }
            $stmt->close();
        }
    }
}
