<?php
session_start();
include 'db_config.php';

if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.html");
    exit();
}

$id = $_GET['id'];
$stmt = $conn->prepare("SELECT f.*, c.name AS course FROM feedback f JOIN course c ON f.course_id = c.id WHERE f.id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$feedback = $stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
  <title>Feedback Details</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
  <h3>Feedback Details</h3>
  <p><strong>Student:</strong> <?= htmlspecialchars($feedback['student_name']) ?></p>
  <p><strong>Course:</strong> <?= htmlspecialchars($feedback['course']) ?></p>
  <p><strong>Lecturer Rating:</strong> <?= $feedback['lecturer_rating'] ?></p>
  <p><strong>Tutor Rating:</strong> <?= $feedback['tutor_rating'] ?></p>
  <p><strong>Comment:</strong><br><?= nl2br(htmlspecialchars($feedback['comment'])) ?></p>
  <a href="admin_dashboard.php" class="btn btn-secondary mt-3">← Back</a>
</body>
</html>
