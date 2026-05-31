<?php
if ($userRole === 'teacher') {
    require __DIR__ . '/teacher/dashboard.php';
} elseif ($userRole === 'student') {
    require __DIR__ . '/student/dashboard.php';
} else {
    // Fallback for admin or unhandled roles (Admin already has its own controller, but just in case)
    echo "<h2>Invalid Dashboard Role</h2>";
}
?>
