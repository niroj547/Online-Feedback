<?php
include 'db_config.php';

// Get form data
$studentName = trim($_POST['studentName'] ?? '');
$course_id = intval($_POST['course_id'] ?? 0);
$lecturer_rating = intval($_POST['lecturer'] ?? 0);
$tutor_rating = intval($_POST['tutor'] ?? 0);
$comment = trim($_POST['comment'] ?? '');
$anonymous = isset($_POST['anonymous']) ? 1 : 0;

if ($anonymous) {
    $studentName = '';
}

// Check for duplicate submissions within the past week (only if not anonymous)
if (!$anonymous) {
    $lastWeek = date('Y-m-d', strtotime('-7 days'));
    $stmt_check = $conn->prepare("SELECT id FROM feedback WHERE course_id = ? AND student_name = ? AND feedback_date >= ?");
    $stmt_check->bind_param("iss", $course_id, $studentName, $lastWeek);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        echo "<script>
          alert('You have already submitted feedback for this course within the last week.');
          window.location.href='feedback_form.html';
        </script>";
        exit();
    }
}

// Insert feedback if input is valid
if ($course_id > 0 && $lecturer_rating >= 1 && $lecturer_rating <= 5 && $tutor_rating >= 1 && $tutor_rating <= 5) {
    $stmt = $conn->prepare("INSERT INTO feedback 
        (student_name, course_id, lecturer_rating, tutor_rating, comment, anonymous_mode, feedback_date) 
        VALUES (?, ?, ?, ?, ?, ?, NOW())");

    $stmt->bind_param("siiisi", $studentName, $course_id, $lecturer_rating, $tutor_rating, $comment, $anonymous);

    if ($stmt->execute()) {
        echo "<script>
          alert('Feedback submitted successfully! Thank you for your input.');
          window.location.href='course_select.php';
        </script>";
    } else {
        error_log("Database error: " . $stmt->error);
        echo "<script>alert('Failed to submit feedback.'); window.history.back();</script>";
    }
    $stmt->close();
} else {
    echo "<script>alert('Please fill all fields correctly.'); window.history.back();</script>";
}
?>
