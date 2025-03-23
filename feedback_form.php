<?php
include 'db.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = $_SESSION['user_id'];
    $course_id = $_POST['course_id'];
    $feedback = $_POST['feedback'];
    $anonymous = isset($_POST['anonymous']) ? 1 : 0;

    $sql = "INSERT INTO feedback (student_id, course_id, feedback, anonymous) VALUES ('$student_id', '$course_id', '$feedback', '$anonymous')";
    
    if ($conn->query($sql) === TRUE) {
        echo "Feedback submitted.";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
