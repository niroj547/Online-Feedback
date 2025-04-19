<?php
include 'db_config.php';

$course_id = intval($_POST['course_id'] ?? 0);
$success = $error = '';
$showSuccessModal = false;
$showErrorModal = false;
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

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['submit'])) {
    $student_name = trim($_POST['student_name'] ?? '');
    $lecturer_id = $course_id;
    $tutor_id = $course_id;
    $lecturer_rating = intval($_POST['lecturer'] ?? 0);
    $tutor_rating = intval($_POST['tutor'] ?? 0);
    $lecturer_comment = trim($_POST['lecturer_comment'] ?? '');
    $tutor_comment = trim($_POST['tutor_comment'] ?? '');
    $anonymous = isset($_POST['anonymous']) ? 1 : 0;

    if ($anonymous) $student_name = '';

    $allow_submit = true;

    if (!$anonymous && $student_name === '') {
        $error = "Please enter your name or select anonymous.";
        $showErrorModal = true;
    } else {
        if (!$anonymous) {
            $three_days_ago = date('Y-m-d H:i:s', strtotime('-3 days'));
            $check_sql = $conn->prepare("SELECT id FROM feedback WHERE student_name = ? AND course_id = ? AND feedback_date >= ?");
            $check_sql->bind_param("sis", $student_name, $course_id, $three_days_ago);
            $check_sql->execute();
            $check_result = $check_sql->get_result();

            if ($check_result->num_rows > 0) {
                $error = "You have already submitted feedback for this course in the last 3 days.";
                $allow_submit = false;
                $showErrorModal = true;
            }

            $check_sql->close();
        }

        if ($allow_submit && $lecturer_rating && $tutor_rating) {
            $stmt = $conn->prepare("INSERT INTO feedback 
                (student_name, course_id, lecturer_id, tutor_id, lecturer_rating, tutor_rating, lecturer_comment, tutor_comment, anonymous_mode, verified, feedback_date)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 0, NOW())");

            $stmt->bind_param("siiiisssi", $student_name, $course_id, $lecturer_id, $tutor_id, $lecturer_rating, $tutor_rating, $lecturer_comment, $tutor_comment, $anonymous);

            if ($stmt->execute()) {
                $success = "✅ Feedback submitted successfully.";
                $showSuccessModal = true;
            } else {
                $error = "❌ Error: " . $stmt->error;
                $showErrorModal = true;
            }

            $stmt->close();
        } elseif (!$error) {
            $error = "Please rate both the lecturer and the tutor.";
            $showErrorModal = true;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Rate <?= htmlspecialchars($course_name) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <style>
    .star-rating input[type="radio"] { display: none; }
    .star-rating label {
      font-size: 2rem;
      color: #ccc;
      cursor: pointer;
    }
    .star-rating input[type="radio"]:checked ~ label,
    .star-rating label:hover,
    .star-rating label:hover ~ label {
      color: gold;
    }
    .form-section { margin-bottom: 1.5rem; }
  </style>
</head>
<body class="bg-light p-4">
<div class="container">
  <h2 class="mb-4">Feedback for <?= htmlspecialchars($course_name) ?></h2>

  <form method="post" id="feedbackForm">
    <input type="hidden" name="course_id" value="<?= $course_id ?>">
    <input type="hidden" name="lecturer_id" value="<?= $course_id ?>">
    <input type="hidden" name="tutor_id" value="<?= $course_id ?>">

    <div class="form-section">
      <label class="form-label">Your Name</label>
      <input type="text" name="student_name" class="form-control" placeholder="Enter your name">
    </div>

    <div class="form-check mb-3">
      <input type="checkbox" name="anonymous" id="anonymous" class="form-check-input" onchange="toggleAnonymous()">
      <label for="anonymous" class="form-check-label">Submit anonymously</label>
    </div>

    <div class="form-section">
      <label class="form-label"><strong>Lecturer:</strong> <?= htmlspecialchars($lecturer_name) ?></label>
      <div class="star-rating d-flex flex-row-reverse justify-content-start">
        <?php for ($i = 5; $i >= 1; $i--): ?>
          <input type="radio" id="lecturer<?= $i ?>" name="lecturer" value="<?= $i ?>" required>
          <label for="lecturer<?= $i ?>">★</label>
        <?php endfor; ?>
      </div>
    </div>

    <div class="form-section">
      <label class="form-label">Lecturer Feedback</label>
      <textarea name="lecturer_comment" class="form-control" rows="3"></textarea>
    </div>

    <div class="form-section">
      <label class="form-label"><strong>Tutor:</strong> <?= htmlspecialchars($tutor_name) ?></label>
      <div class="star-rating d-flex flex-row-reverse justify-content-start">
        <?php for ($i = 5; $i >= 1; $i--): ?>
          <input type="radio" id="tutor<?= $i ?>" name="tutor" value="<?= $i ?>" required>
          <label for="tutor<?= $i ?>">★</label>
        <?php endfor; ?>
      </div>
    </div>

    <div class="form-section">
      <label class="form-label">Tutor Feedback</label>
      <textarea name="tutor_comment" class="form-control" rows="3"></textarea>
    </div>

    <button type="submit" name="submit" class="btn btn-primary">Submit Feedback</button>
  </form>
</div>

<!-- ✅ Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title" id="successModalLabel">Feedback Submitted!</h5>
      </div>
      <div class="modal-body">
        ✅ Your feedback was submitted successfully.<br><br>
        Would you like to rate another course?
      </div>
      <div class="modal-footer">
        <a href="course_select.php" class="btn btn-primary">Yes, rate another</a>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No, stay here</button>
      </div>
    </div>
  </div>
</div>

<!-- ❌ Error Modal -->
<div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="errorModalLabel">Error</h5>
      </div>
      <div class="modal-body">
        <?= htmlspecialchars($error) ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Okay</button>
      </div>
    </div>
  </div>
</div>

<script>
  function toggleAnonymous() {
    const anon = document.getElementById("anonymous").checked;
    const nameField = document.querySelector("input[name='student_name']");
    nameField.disabled = anon;
    if (anon) nameField.value = '';
  }

  window.onload = function () {
    toggleAnonymous();

    <?php if ($showSuccessModal): ?>
      new bootstrap.Modal(document.getElementById('successModal')).show();
    <?php endif; ?>

    <?php if ($showErrorModal): ?>
      new bootstrap.Modal(document.getElementById('errorModal')).show();
    <?php endif; ?>
  };
</script>
</body>
</html>
