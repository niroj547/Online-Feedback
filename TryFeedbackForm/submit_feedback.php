<?php
include 'db_config.php';

$studentName = $_POST['studentName'];
$academicYear = $_POST['academicYear'];
$semester = $_POST['semester'];
$feedbackDate = $_POST['feedbackDate'];
$section = $_POST['section'];
$anonymous = isset($_POST['anonymous']) ? 1 : 0;

// Insert feedback into the database
$sql = "INSERT INTO feedback (student_name, academic_year, semester, feedback_date, section, anonymous_mode)
        VALUES (?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssi", $studentName, $academicYear, $semester, $feedbackDate, $section, $anonymous);

if ($stmt->execute()) {
    echo "<script>window.location.href='course_select.html';</script>";
} else {
    echo "<script>alert('Something went wrong!'); window.history.back();</script>";
}
?>
