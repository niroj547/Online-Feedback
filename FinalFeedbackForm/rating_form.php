<?php
include 'db_config.php';

$course_id = $_POST['course_id'] ?? 0;
$course_name = '';
$success = '';
$error = '';

if ($course_id > 0) {
    $stmt = $conn->prepare("SELECT name FROM course WHERE id = ?");
    $stmt->bind_param("i", $course_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $course = $result->fetch_assoc();
        $course_name = $course['name'];
    }
    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['submit'])) {
    $student_name = $_POST['student_name'] ?? '';
    $lecturer_rating = $_POST['lecturer'] ?? 0;
    $tutor_rating = $_POST['tutor'] ?? 0;
    $comment = $_POST['comment'] ?? '';
    $anonymous = isset($_POST['anonymous']) ? 1 : 0;

    if ($anonymous) {
        $student_name = '';
    }

    if ($course_id > 0 && $lecturer_rating >= 1 && $lecturer_rating <= 5 && $tutor_rating >= 1 && $tutor_rating <= 5) {
        $stmt = $conn->prepare("INSERT INTO feedback (student_name, course_id, lecturer_rating, tutor_rating, comment, anonymous_mode) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("siiisi", $student_name, $course_id, $lecturer_rating, $tutor_rating, $comment, $anonymous);
        if ($stmt->execute()) {
            $success = "Feedback submitted successfully!";
        } else {
            $error = "Failed to submit feedback.";
        }
        $stmt->close();
    } else {
        $error = "Please fill all fields correctly.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Rate <?= htmlspecialchars($course_name) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .star-rating input[type="radio"] {
      display: none;
    }
    .star-rating label {
      font-size: 2rem;
      color: #ccc;
      cursor: pointer;
    }
    .star-rating input[type="radio"]:checked ~ label {
      color: gold;
    }
    .star-rating label:hover,
    .star-rating label:hover ~ label {
      color: gold;
    }
  </style>
</head>
<body class="bg-light p-4">
<div class="container">
  <h3 class="mb-4">Rate: <?= htmlspecialchars($course_name) ?></h3>

  <?php if ($success): ?>
    <div class="alert alert-success"><?= $success ?></div>
  <?php elseif ($error): ?>
    <div class="alert alert-danger"><?= $error ?></div>
  <?php endif; ?>

  <form method="post">
    <input type="hidden" name="course_id" value="<?= htmlspecialchars($course_id) ?>">

    <div class="mb-3">
      <label for="student_name" class="form-label">Your Name</label>
      <input type="text" name="student_name" id="student_name" class="form-control" placeholder="Enter your name">
    </div>

    <div class="form-check mb-3">
      <input type="checkbox" name="anonymous" id="anonymous" class="form-check-input" onchange="toggleAnonymous()">
      <label for="anonymous" class="form-check-label">Submit anonymously</label>
    </div>

    <div class="mb-3">
      <label class="form-label">Lecturer Rating</label>
      <div class="star-rating d-flex flex-row-reverse justify-content-start">
        <?php for ($i = 5; $i >= 1; $i--): ?>
          <input type="radio" id="lecturer<?= $i ?>" name="lecturer" value="<?= $i ?>" required>
          <label for="lecturer<?= $i ?>">★</label>
        <?php endfor; ?>
      </div>
    </div>

    <div class="mb-3">
      <label class="form-label">Tutor Rating</label>
      <div class="star-rating d-flex flex-row-reverse justify-content-start">
        <?php for ($i = 5; $i >= 1; $i--): ?>
          <input type="radio" id="tutor<?= $i ?>" name="tutor" value="<?= $i ?>" required>
          <label for="tutor<?= $i ?>">★</label>
        <?php endfor; ?>
      </div>
    </div>

    <div class="mb-3">
      <label class="form-label">Comment</label>
      <textarea name="comment" class="form-control" rows="4" placeholder="Share your thoughts..."></textarea>
    </div>

    <button type="submit" name="submit" class="btn btn-primary">Submit Feedback</button>
  </form>
</div>

<script>
  function toggleAnonymous() {
    const isAnon = document.getElementById('anonymous').checked;
    const nameField = document.getElementById('student_name');
    if (isAnon) {
      nameField.value = '';
      nameField.disabled = true;
    } else {
      nameField.disabled = false;
    }
  }
</script>
</body>
</html>
