# Database Connection Subskill

This subskill outlines the implementation and management of the database layer in our PHP MVC project. All connection logic is consolidated inside `App\Core\Database` using a class-based approach.

## Database Class Specification (`src/Core/Database.php`)

The `Database` class connects to the MariaDB server using the global parameters defined in `config/config.php`. Upon the first connection, it automatically verifies and builds the database and requisite tables if they do not exist:

```php
<?php
namespace App\Core;

use mysqli;

class Database {
    private static ?mysqli $connection = null;

    /**
     * Get or initialize the database connection instance (Singleton)
     */
    public static function getConnection(): mysqli {
        if (self::$connection === null) {
            self::initialize();
        }
        return self::$connection;
    }

    /**
     * Connect and run migrations/auto-schema setup
     */
    private static function initialize(): void {
        // 1. Connect to DBMS
        $conn = @new mysqli(DB_HOST, DB_USER, DB_PASS);

        if ($conn->connect_error) {
            die("Database Server Connection Failed: " . $conn->connect_error);
        }

        // 2. Create the Database if missing
        $dbName = $conn->real_escape_string(DB_NAME);
        $createDbQuery = "CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
        if (!$conn->query($createDbQuery)) {
            die("Error creating database: " . $conn->error);
        }

        // 3. Select active database
        if (!$conn->select_db(DB_NAME)) {
            die("Error selecting database: " . $conn->error);
        }

        // 4. Create Tables (Automatic Migrations)
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
```

## How to Obtain Connection Instance

Whenever a controller or model needs to interact with the database, it obtains the unified global connection through the static getter:

```php
namespace App\Controllers;

use App\Core\Database;

class MyController {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }
}
```
