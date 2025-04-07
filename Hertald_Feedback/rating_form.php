<?php
// rating_form.php - Final Fully Working Version
session_start();
include 'db_config.php';

if (!isset($_SESSION['student_name'])) {
    header("Location: student_login.html");
    exit();
}

$student_name = $_SESSION['student_name'];
$course_id = intval($_POST['course_id'] ?? 0);
$success = $error = '';
$course_name = '';
$lecturer_name = '';
$tutor_name = '';

if ($course_id > 0) {
    $stmt = $conn->prepare("SELECT name FROM course WHERE id = ?");
    $stmt->bind_param("i", $course_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $course_name = $result->fetch_assoc()['name'] ?? '';
    $stmt->close();

    $lecturer_result = $conn->query("SELECT name FROM lecturer WHERE id = $course_id");
    $lecturer_name = $lecturer_result->num_rows > 0 ? $lecturer_result->fetch_assoc()['name'] : 'Not assigned';

    $tutor_result = $conn->query("SELECT name FROM tutor WHERE id = $course_id");
    $tutor_name = $tutor_result->num_rows > 0 ? $tutor_result->fetch_assoc()['name'] : 'Not assigned';
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['submit'])) {
    $lecturer_rating = intval($_POST['lecturer'] ?? 0);
    $tutor_rating = intval($_POST['tutor'] ?? 0);
    $lecturer_comment = trim($_POST['lecturer_comment'] ?? '');
    $tutor_comment = trim($_POST['tutor_comment'] ?? '');
    $anonymous = isset($_POST['anonymous']) ? 1 : 0;
    $final_student_name = $anonymous ? '' : $student_name;

    $allow_submit = true;

    if (!$anonymous) {
        $three_days_ago = date('Y-m-d H:i:s', strtotime('-3 days'));
        $stmt = $conn->prepare("SELECT id FROM feedback WHERE student_name = ? AND course_id = ? AND feedback_date >= ?");
        $stmt->bind_param("sis", $student_name, $course_id, $three_days_ago);
        $stmt->execute();
        $check_result = $stmt->get_result();
        if ($check_result->num_rows > 0) {
            $error = "⏳ You already submitted feedback for this course in the last 3 days.";
            $allow_submit = false;
        }
        $stmt->close();
    }

    if ($allow_submit && $lecturer_rating && $tutor_rating) {
        $stmt = $conn->prepare("INSERT INTO feedback 
            (student_name, course_id, lecturer_id, tutor_id, lecturer_rating, tutor_rating, lecturer_comment, tutor_comment, anonymous_mode, verified, feedback_date)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 0, NOW())");
        $stmt->bind_param("siiiisssi", $final_student_name, $course_id, $course_id, $course_id, $lecturer_rating, $tutor_rating, $lecturer_comment, $tutor_comment, $anonymous);
        if ($stmt->execute()) {
            $success = "✅ Feedback submitted successfully.";
        } else {
            $error = "❌ Error: " . $stmt->error;
        }
        $stmt->close();
    } elseif (!$error) {
        $error = "Please rate both the lecturer and the tutor.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Rate <?= htmlspecialchars($course_name) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="bg-light p-4">
<div class="container">
  <h2 class="mb-4">Feedback for <?= htmlspecialchars($course_name) ?></h2>

  <?php if ($success): ?>
    <div class="alert alert-success"><?= $success ?></div>
    <script>
      setTimeout(() => {
        if (confirm("✅ Feedback submitted!\nDo you want to rate another course?")) {
          window.location.href = "course_select.php";
        }
      }, 600);
    </script>
  <?php elseif ($error): ?>
    <div class="alert alert-danger"><?= $error ?></div>
  <?php endif; ?>

  <?php if (!$success): ?>
  <form method="post">
    <input type="hidden" name="course_id" value="<?= $course_id ?>">

    <div class="mb-3">
      <label class="form-label">Lecturer: <?= htmlspecialchars($lecturer_name) ?></label>
      <div class="form-check form-check-inline">
        <?php for ($i = 1; $i <= 5; $i++): ?>
          <input class="form-check-input" type="radio" name="lecturer" id="lecturer<?= $i ?>" value="<?= $i ?>" required>
          <label class="form-check-label me-2" for="lecturer<?= $i ?>"><?= $i ?></label>
        <?php endfor; ?>
      </div>
    </div>

    <div class="mb-3">
      <label class="form-label">Lecturer Feedback</label>
      <textarea name="lecturer_comment" class="form-control" rows="2"></textarea>
    </div>

    <div class="mb-3">
      <label class="form-label">Tutor: <?= htmlspecialchars($tutor_name) ?></label>
      <div class="form-check form-check-inline">
        <?php for ($i = 1; $i <= 5; $i++): ?>
          <input class="form-check-input" type="radio" name="tutor" id="tutor<?= $i ?>" value="<?= $i ?>" required>
          <label class="form-check-label me-2" for="tutor<?= $i ?>"><?= $i ?></label>
        <?php endfor; ?>
      </div>
    </div>

    <div class="mb-3">
      <label class="form-label">Tutor Feedback</label>
      <textarea name="tutor_comment" class="form-control" rows="2"></textarea>
    </div>

    <div class="form-check mb-3">
      <input class="form-check-input" type="checkbox" name="anonymous" id="anonymous">
      <label class="form-check-label" for="anonymous">Submit anonymously</label>
    </div>

    <button type="submit" name="submit" class="btn btn-primary">Submit Feedback</button>
  </form>
  <?php endif; ?>
</div>
</body>
</html>