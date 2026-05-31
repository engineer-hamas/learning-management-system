<?php
require_once __DIR__ . '/../config/config.php';

echo "🔄 Resetting database '" . DB_NAME . "'...\n";

// Connect to MySQL server without selecting the database
$db = new mysqli(DB_HOST, DB_USER, DB_PASS);

if ($db->connect_error) {
    die("❌ Connection failed: " . $db->connect_error . "\n");
}

// Drop the database if it exists
if ($db->query("DROP DATABASE IF EXISTS `" . DB_NAME . "`") === TRUE) {
    echo "✅ Dropped database successfully.\n";
} else {
    die("❌ Error dropping database: " . $db->error . "\n");
}

// Recreate the database
if ($db->query("CREATE DATABASE `" . DB_NAME . "`") === TRUE) {
    echo "✅ Created database successfully.\n";
} else {
    die("❌ Error creating database: " . $db->error . "\n");
}

$db->close();

echo "🎉 Database reset complete! The schema and seeder will run automatically on the next page load.\n";
