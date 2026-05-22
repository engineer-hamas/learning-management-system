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

        // 4. Create the 'users' table if it doesn't exist
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

        self::$connection = $conn;
    }
}
Class_Alias('App\\Core\\Database', 'Database');
