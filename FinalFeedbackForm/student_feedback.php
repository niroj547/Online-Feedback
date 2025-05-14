<?php
include 'db_config.php';

$studentName = $_POST['studentName'];
$academicYear = $_POST['academicYear'];
$semester = $_POST['semester'];
$feedbackDate = $_POST['feedbackDate'];
$section = $_POST['section'];
$anonymous = isset($_POST['anonymous']) ? 1 : 0;
$course = $_POST['course'];
$lecturer = $_POST['lecturer'];
$tutor = $_POST['tutor'];
$comment = $_POST['comment'];

$sql = "INSERT INTO feedback (student_name, academic_year, semester, feedback_date, section, anonymous_mode, course, lecturer_rating, tutor_rating, comment)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssiisis", $studentName, $academicYear, $semester, $feedbackDate, $section, $anonymous, $course, $lecturer, $tutor, $comment);

if ($stmt->execute()) {
    echo "Feedback submitted successfully!";
} else {
    echo "Error: " . $stmt->error;
}
?>
