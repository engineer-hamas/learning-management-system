<?php
require_once __DIR__ . '/config/config.php';
$conn = @new mysqli(DB_HOST, DB_USER, DB_PASS);
if ($conn->connect_error) {
    die("Connection Failed: " . $conn->connect_error);
}
if ($conn->query("DROP DATABASE IF EXISTS " . DB_NAME)) {
    echo "Database dropped successfully.";
} else {
    echo "Error dropping database: " . $conn->error;
}
