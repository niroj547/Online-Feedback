<?php
session_start();
include 'db_config.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_SESSION['student_id'])) {
    $student_id = $_SESSION['student_id'];
    $feedback_data = $_POST; // Collect feedback data

    // Save feedback data to the database (implement as needed)
    $stmt = $conn->prepare("INSERT INTO feedback (student_id, feedback_data) VALUES (?, ?)");
    $feedback_json = json_encode($feedback_data);
    $stmt->bind_param("is", $student_id, $feedback_json);
    $stmt->execute();

    // Update last feedback time
    $update_stmt = $conn->prepare("UPDATE students SET last_feedback_time = NOW() WHERE id = ?");
    $update_stmt->bind_param("i", $student_id);
    $update_stmt->execute();

    echo "<script>alert('Feedback submitted successfully!'); window.location.href='student_login.html';</script>";
} else {
    echo "<script>alert('Unauthorized access.'); window.location.href='student_login.html';</script>";
}
?>
