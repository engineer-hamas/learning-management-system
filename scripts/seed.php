<?php
/**
 * Seed Script
 *
 * Populates the database with default data WITHOUT dropping/resetting it.
 * This script is idempotent — safe to run multiple times. It only inserts
 * data when a table is empty (uses COUNT() guards in the Seeder class).
 *
 * Usage:
 *   php scripts/seed.php
 *   bun run seed
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../src/Core/Autoloader.php';
\App\Core\Autoloader::register();

$line = str_repeat('─', 50);
echo "\n{$line}\n";
echo "  🌱  LMS Database Seeder\n";
echo "{$line}\n\n";

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("  ❌  Connection failed: " . $conn->connect_error . "\n\n");
}

echo "  Seeding data into '" . DB_NAME . "'...\n\n";

\App\Core\Database\Seeder::seed($conn);

$conn->close();

echo "  ✅  Seeding complete!\n";
echo "\n  Default credentials (password: password)\n";
echo "  ┌──────────────────────────────────────┐\n";
echo "  │  admin@example.com   →  Admin        │\n";
echo "  │  teacher1@example.com → Teacher One  │\n";
echo "  │  student1@example.com → Student One  │\n";
echo "  └──────────────────────────────────────┘\n";
echo "{$line}\n\n";
