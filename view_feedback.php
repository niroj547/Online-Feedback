<?php
include 'db_config.php';

// Actions
if (isset($_GET['verify_id'])) {
    $conn->query("UPDATE feedback SET verified = 1 WHERE id = " . intval($_GET['verify_id']));
    header("Location: view_feedback.php");
    exit();
}
if (isset($_GET['unverify_id'])) {
    $conn->query("UPDATE feedback SET verified = 0 WHERE id = " . intval($_GET['unverify_id']));
    header("Location: view_feedback.php");
    exit();
}
if (isset($_GET['delete_id'])) {
    $conn->query("DELETE FROM feedback WHERE id = " . intval($_GET['delete_id']));
    header("Location: view_feedback.php");
    exit();
}

// Fetch single view
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $feedback = $conn->query("SELECT f.*, 
        c.name AS course_name, 
        l.name AS lecturer_name, 
        t.name AS tutor_name 
        FROM feedback f
        LEFT JOIN course c ON f.course_id = c.id
        LEFT JOIN lecturer l ON f.lecturer_id = l.id
        LEFT JOIN tutor t ON f.tutor_id = t.id
        WHERE f.id = $id")->fetch_assoc();
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>View Feedback</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">

<?php if (isset($feedback)): ?>
  <!-- Single Feedback View -->
  <h2 class="mb-3">Feedback Details</h2>
  <div class="card mb-4">
    <div class="card-header bg-primary text-white">Report</div>
    <div class="card-body">
      <p><strong>Course:</strong> <?= htmlspecialchars($feedback['course_name'] ?? '') ?></p>
      <p><strong>Student:</strong> <?= $feedback['anonymous_mode'] ? 'Anonymous' : htmlspecialchars($feedback['student_name'] ?? '') ?></p>
      <p><strong>Date:</strong> <?= htmlspecialchars($feedback['feedback_date'] ?? '') ?></p>
      <hr>
      <p><strong>Lecturer:</strong> <?= htmlspecialchars($feedback['lecturer_name'] ?? '') ?></p>
      <p><strong>Lecturer Rating:</strong> <?= str_repeat('★', (int)$feedback['lecturer_rating']) ?></p>
      <p><strong>Lecturer Feedback:</strong><br><?= nl2br(htmlspecialchars((string)$feedback['lecturer_comment'])) ?: '<i>No comment</i>' ?></p>
      <hr>
      <p><strong>Tutor:</strong> <?= htmlspecialchars($feedback['tutor_name'] ?? '') ?></p>
      <p><strong>Tutor Rating:</strong> <?= str_repeat('★', (int)$feedback['tutor_rating']) ?></p>
      <p><strong>Tutor Feedback:</strong><br><?= nl2br(htmlspecialchars((string)$feedback['tutor_comment'])) ?: '<i>No comment</i>' ?></p>
    </div>
    <div class="card-footer d-flex justify-content-between flex-wrap gap-2">
      <?php if ($feedback['verified']): ?>
        <a href="?unverify_id=<?= $feedback['id'] ?>" class="btn btn-warning">Unverify</a>
      <?php else: ?>
        <a href="?verify_id=<?= $feedback['id'] ?>" class="btn btn-success">Verify</a>
      <?php endif; ?>
      <a href="?delete_id=<?= $feedback['id'] ?>" class="btn btn-danger" onclick="return confirm('Delete this feedback?')">Delete</a>
      <a href="view_feedback.php" class="btn btn-secondary">Back</a>
    </div>
  </div>

<?php else: ?>
  <!-- All Feedback Table -->
  <h2 class="mb-3">All Feedback</h2>
  <?php
  $result = $conn->query("SELECT f.id, f.feedback_date, f.student_name, f.anonymous_mode, f.verified, c.name AS course_name 
    FROM feedback f
    LEFT JOIN course c ON f.course_id = c.id
    ORDER BY f.feedback_date DESC");
  ?>

  <?php if ($result->num_rows > 0): ?>
    <table class="table table-bordered bg-white">
      <thead class="table-dark">
        <tr>
          <th>Date</th>
          <th>Course</th>
          <th>Student</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
      <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($row['feedback_date'] ?? '') ?></td>
          <td><?= htmlspecialchars($row['course_name'] ?? '') ?></td>
          <td><?= $row['anonymous_mode'] ? '<i>Anonymous</i>' : htmlspecialchars($row['student_name'] ?? '') ?></td>
          <td>
            <?= $row['verified'] ? '<span class="badge bg-success">Verified</span>' : '<span class="badge bg-warning text-dark">Pending</span>' ?>
          </td>
          <td>
            <a href="?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary">View</a>
            <a href="?delete_id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this feedback?')">Delete</a>
          </td>
        </tr>
      <?php endwhile; ?>
      </tbody>
    </table>
  <?php else: ?>
    <div class="alert alert-info">No feedback found.</div>
  <?php endif; ?>
<?php endif; ?>

</div>
</body>
</html>
