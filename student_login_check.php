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

    // Prepare statement to check email and plain password
    $stmt = $conn->prepare("SELECT id, email FROM students WHERE email = ? AND password = ?");
    if (!$stmt) {
        echo "<script>alert('Database error.'); window.history.back();</script>";
        exit();
    }

    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        $_SESSION['student_id'] = $user['id'];
        $_SESSION['student'] = $user['email'];
        header("Location: feedback_form.php");
        exit();
    } else {
        echo "<script>alert('Invalid email or password.'); window.history.back();</script>";
    }

    $stmt->close();
} else {
    echo "<script>alert('Invalid request method.');</script>";
}
?>
