<?php
include 'db_config.php';

$studentName = trim($_POST['studentName'] ?? '');
$academicYear = trim($_POST['academicYear'] ?? '');
$semester = trim($_POST['semester'] ?? '');
$feedbackDate = $_POST['feedbackDate'] ?? '';
$section = trim($_POST['section'] ?? '');
$anonymous = isset($_POST['anonymous']) ? 1 : 0;
$course = trim($_POST['course'] ?? '');
$lecturer = intval($_POST['lecturer'] ?? 0);
$tutor = intval($_POST['tutor'] ?? 0);
$comment = trim($_POST['comment'] ?? '');

// Check if the course exists
$stmt = $conn->prepare("SELECT id FROM course WHERE name = ?");
$stmt->bind_param("s", $course);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $courseRow = $result->fetch_assoc();
    $courseId = $courseRow['id'];
    
    $sql = "INSERT INTO feedback (student_name, academic_year, semester, feedback_date, section, anonymous_mode, course_id, lecturer_rating, tutor_rating, comment)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssiiis", $studentName, $academicYear, $semester, $feedbackDate, $section, $anonymous, $courseId, $lecturer, $tutor, $comment);
    
    if ($stmt->execute()) {
        echo "Feedback submitted successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
} else {
    echo "Error: Course not found.";
}
?>