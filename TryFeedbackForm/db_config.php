<?php
$host = "localhost";
$user = "root";
$password = "NewStrongPassword";
$database = "feedback_system";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
