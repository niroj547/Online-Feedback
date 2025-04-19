<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.html");
    exit();
}
include 'db_config.php';

$view_all = isset($_GET['view_all']);

$sql = "
    SELECT f.id, f.student_name, f.lecturer_rating, f.tutor_rating, f.comment, c.name AS course 
    FROM feedback f 
    JOIN course c ON f.course_id = c.id
";

if (!$view_all) {
    $sql .= " WHERE f.verified = 1";
}

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Admin Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-white p-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Feedback Records</h2>
    <div class="d-flex gap-2">
      <a href="?view_all=1" class="btn btn-outline-primary btn-sm">View All Feedback</a>
      <a href="admin_dashboard.php" class="btn btn-outline-secondary btn-sm">Verified Only</a>
      <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
    </div>
  </div>

  <div class="mb-3">
    <a href="admin_crud_courses.php" class="btn btn-outline-primary btn-sm">Manage Courses</a>
    <a href="admin_crud_lecturers.php" class="btn btn-outline-primary btn-sm">Manage Lecturers</a>
    <a href="admin_crud_tutors.php" class="btn btn-outline-primary btn-sm">Manage Tutors</a>
    <a href="admin_crud_students.php" class="btn btn-outline-primary btn-sm">Manage Students</a>
    <a href="export_feedback_csv.php" class="btn btn-success btn-sm">Export CSV</a>
  </div>

  <table class="table table-striped table-bordered">
    <thead class="table-dark">
      <tr>
        <th>Student</th>
        <th>Course</th>
        <th>Lecturer</th>
        <th>Tutor</th>
        <th>Comment</th>
        <th>Details</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($row['student_name'] ?? '') ?></td>
          <td><?= htmlspecialchars($row['course'] ?? '') ?></td>
          <td><?= htmlspecialchars($row['lecturer_rating'] ?? 'N/A') ?></td>
          <td><?= htmlspecialchars($row['tutor_rating'] ?? 'N/A') ?></td>
          <td><?= htmlspecialchars(substr($row['comment'] ?? '', 0, 30)) ?>...</td>
          <td><a href="view_feedback.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-info">View</a></td>
        </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="6" class="text-center">No feedback submitted yet.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</body>
</html>
