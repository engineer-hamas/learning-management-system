<?php
/**
 * Fresh Start Script
 *
 * Performs a complete database reset:
 *   1. Drops the database (if it exists)
 *   2. Recreates the database
 *   3. Runs all schema migrations (creates all tables)
 *   4. Seeds the database with default data
 *
 * Usage:
 *   php scripts/fresh-start.php
 *   bun run fresh
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../src/Core/Autoloader.php';
\App\Core\Autoloader::register();

$line = str_repeat('─', 50);
echo "\n{$line}\n";
echo "  🚀  LMS Fresh Start\n";
echo "{$line}\n\n";

// ── Step 1: Drop and recreate the database ──────────────────────────────────
echo "  [1/3] Resetting database '" . DB_NAME . "'...\n";

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS);
if ($conn->connect_error) {
    die("  ❌  Connection failed: " . $conn->connect_error . "\n");
}

if ($conn->query("DROP DATABASE IF EXISTS `" . DB_NAME . "`") === true) {
    echo "       ✓ Dropped old database.\n";
} else {
    die("  ❌  Failed to drop database: " . $conn->error . "\n");
}

if ($conn->query("CREATE DATABASE `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci") === true) {
    echo "       ✓ Created fresh database.\n";
} else {
    die("  ❌  Failed to create database: " . $conn->error . "\n");
}

$conn->close();

// ── Step 2: Run schema migrations ───────────────────────────────────────────
echo "\n  [2/3] Running schema migrations...\n";

$conn2 = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn2->connect_error) {
    die("  ❌  Connection failed: " . $conn2->connect_error . "\n");
}

\App\Core\Database\SchemaMigration::migrate($conn2);
echo "       ✓ All tables created.\n";

// ── Step 3: Seed default data ────────────────────────────────────────────────
echo "\n  [3/3] Seeding default data...\n";

\App\Core\Database\Seeder::seed($conn2);
echo "       ✓ Departments, subjects, and users seeded.\n";

$conn2->close();

// ── Done ─────────────────────────────────────────────────────────────────────
echo "\n{$line}\n";
echo "  ✅  Fresh start complete!\n";
echo "\n  Default credentials (password: password)\n";
echo "  ┌──────────────────────────────────────┐\n";
echo "  │  admin@example.com   →  Admin        │\n";
echo "  │  teacher1@example.com → Teacher One  │\n";
echo "  │  student1@example.com → Student One  │\n";
echo "  └──────────────────────────────────────┘\n";
echo "{$line}\n\n";
