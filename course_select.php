<?php
include 'db_config.php';

// Fetch course list
$courses_query = $conn->query("SELECT id, name FROM course");

if (!$courses_query) {
    die("<div style='color:red;'>Database Error: " . $conn->error . "</div>");
}

$courses = $courses_query->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Select Course</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
  <h2 class="mb-4">Select a Course to Rate</h2>

  <form action="rating_form.php" method="post">
    <?php foreach ($courses as $course): ?>
      <div class="form-check mb-2">
        <input class="form-check-input" type="radio" name="course_id" id="course_<?= $course['id'] ?>" value="<?= $course['id'] ?>" required>
        <label class="form-check-label" for="course_<?= $course['id'] ?>">
          <?= htmlspecialchars($course['name']) ?>
        </label>
      </div>
    <?php endforeach; ?>

    <button type="submit" class="btn btn-primary mt-3">Next</button>
  </form>
</div>
</body>
</html>
