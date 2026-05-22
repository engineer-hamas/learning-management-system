<?php
// Configuration settings

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database settings
define('DB_HOST', '127.0.0.1'); // Using IP is often more reliable than localhost on Linux
define('DB_USER', 'root');
define('DB_PASS', '6421');
define('DB_NAME', 'lms');

// Application settings
define('APP_NAME', 'LMS Portal');
define('APP_URL', 'http://localhost:8000');
define('DEBUG_MODE', true);

// Enable error reporting if debug mode is on
if (DEBUG_MODE) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}
