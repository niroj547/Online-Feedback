<?php
session_start();              // Start the session
session_unset();              // Clear all session variables
session_destroy();            // Destroy the session completely

// Redirect to admin login page
header("Location: admin_login.html");
exit();
?>
