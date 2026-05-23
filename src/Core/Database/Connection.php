<?php
namespace App\Core\Database;

use mysqli;

/**
 * Class Connection
 * 
 * Manages the singleton MariaDB server connection and database selection.
 */
class Connection {
    private static ?mysqli $connection = null;

    /**
     * Obtain the singleton database connection
     * 
     * @return mysqli
     */
    public static function get(): mysqli {
        if (self::$connection === null) {
            // 1. Connect to the database server without selecting a database
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

            self::$connection = $conn;
        }
        return self::$connection;
    }
}
