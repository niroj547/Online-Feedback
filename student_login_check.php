<?php
session_start();
include 'db_config.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    
    if (empty($email) || empty($password)) {
        echo "<script>alert('Email and password are required.'); window.history.back();</script>";
        exit();
    }
    
    $stmt = $conn->prepare("SELECT * FROM students WHERE email = ? AND password = ?");
    if (!$stmt) {
        echo "<script>alert('Error preparing statement.');</script>";
        exit();
    }
    
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $_SESSION['student'] = $email;
        echo "<script>window.location.href='feedback_form.php';</script>";
    } else {
        echo "<script>alert('Invalid login. Please try again.'); window.history.back();</script>";
    }
} else {
    echo "<script>alert('Invalid request method.');</script>";
}
?>