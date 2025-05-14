<?php
$host = "localhost";
$user = "root";
$password = "NewStrongPassword";
$database = "feedback_system";
$port = 3307;

$conn = new mysqli($host, $user, $password, $database, $port);

// Error logging setup
$errorLog = __DIR__ . '/error_log.txt';
ini_set('log_errors', 1);
ini_set('error_log', $errorLog);
error_reporting(E_ALL);

// Handle connection error
if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    echo "<div style='color:red;'>Oops! Something went wrong. Please try again later.</div>";
    exit;
}
?>
