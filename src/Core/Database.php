<?php
namespace App\Core;

use mysqli;
use App\Core\Database\Connection;
use App\Core\Database\SchemaMigration;
use App\Core\Database\Seeder;

/**
 * Class Database
 * 
 * Facade for Database operations. Orchestrates Connection, Migration, and Seeding.
 */
class Database {
    private static bool $initialized = false;

    /**
     * Obtain the singleton database connection and ensure schema is initialized
     * 
     * @return mysqli
     */
    public static function getConnection(): mysqli {
        $conn = Connection::get();

        if (!self::$initialized) {
            SchemaMigration::migrate($conn);
            Seeder::seed($conn);
            self::$initialized = true;
        }

        return $conn;
    }
}

// Keep backward compatibility for Class_Alias if needed, though autoloader handles App\Core\Database
Class_Alias('App\\Core\\Database', 'Database');
