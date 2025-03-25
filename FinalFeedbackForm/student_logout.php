<?php
session_start();              // Start the session
session_unset();              // Clear all session variables
session_destroy();            // Destroy the session completely

// Redirect to student login page
header("Location: student_login.html");
exit();
?>
