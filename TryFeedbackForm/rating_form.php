<?php
include 'db_config.php';

$success = $error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  // Validate and sanitize
  $course_id = isset($_POST['course_id']) ? intval($_POST['course_id']) : 0;
  $lecturer_rating = isset($_POST['lecturer']) ? intval($_POST['lecturer']) : 0;
  $tutor_rating = isset($_POST['tutor']) ? intval($_POST['tutor']) : 0;
  $comment = isset($_POST['comment']) ? htmlspecialchars(trim($_POST['comment'])) : "";

  if ($course_id > 0 && $lecturer_rating >= 1 && $lecturer_rating <= 5 && $tutor_rating >= 1 && $tutor_rating <= 5) {
    $sql = "INSERT INTO feedback (course_id, lecturer_rating, tutor_rating, comment)
            VALUES (?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    if ($stmt) {
      $stmt->bind_param("iiis", $course_id, $lecturer_rating, $tutor_rating, $comment);
      if ($stmt->execute()) {
        $success = "✅ Feedback submitted successfully!";
      } else {
        $error = "❌ Failed to submit feedback.";
        error_log("MySQL execute error: " . $stmt->error);
      }
      $stmt->close();
    } else {
      $error = "❌ Prepare statement failed.";
    }
  } else {
    $error = "❌ Please fill all fields correctly.";
  }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Student Feedback Form</title>
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background-image: url('background.jpg'); /* Replace with your chalkboard bg */
      background-size: cover;
      background-position: center;
      height: 100vh;
      color: white;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .rating-container {
      background-color: rgba(0, 0, 0, 0.4);
      padding: 30px;
      border-radius: 15px;
      width: 700px;
    }

    h2 {
      text-align: center;
      font-size: 26px;
      margin-bottom: 10px;
    }

    .rating-section {
      display: flex;
      justify-content: space-between;
      margin: 20px 0;
    }

    .rating-column {
      width: 45%;
    }

    .rating-column h3 {
      margin-bottom: 10px;
    }

    .stars {
      display: flex;
      flex-direction: row-reverse;
      justify-content: flex-end;
    }

    .stars input[type="radio"] {
      display: none;
    }

    .stars label {
      font-size: 25px;
      color: white;
      cursor: pointer;
      transition: color 0.2s;
    }

    .stars input:checked ~ label,
    .stars label:hover,
    .stars label:hover ~ label {
      color: gold;
    }

    .comment-section {
      margin-top: 20px;
      text-align: center;
    }

    textarea {
      width: 100%;
      height: 80px;
      border-radius: 10px;
      padding: 10px;
      font-size: 14px;
      border: none;
      background-color: #d8ccc8;
      color: #333;
    }

    .submit-btn {
      margin-top: 20px;
      padding: 12px 28px;
      font-size: 16px;
      font-weight: bold;
      background-color: #d8ccc8;
      border: none;
      border-radius: 10px;
      cursor: pointer;
      display: block;
      margin-left: auto;
    }

    .success-msg {
      background: #4CAF50;
      padding: 10px;
      border-radius: 8px;
      margin-bottom: 15px;
      color: white;
    }

    .error-msg {
      background: #f44336;
      padding: 10px;
      border-radius: 8px;
      margin-bottom: 15px;
      color: white;
    }
  </style>
</head>
<body>
  <div class="rating-container">
    <h2>Student Feedback Form</h2>
    <p><strong>Course:</strong> Collaborative Development</p>

    <?php if ($success): ?>
      <div class="success-msg"><?= $success ?></div>
    <?php elseif ($error): ?>
      <div class="error-msg"><?= $error ?></div>
    <?php endif; ?>

    <form action="" method="post">
      <input type="hidden" name="course_id" value="1"> <!-- Change ID if needed -->

      <div class="rating-section">
        <div class="rating-column">
          <h3>Lecturer</h3>
          <div class="stars">
            <input type="radio" id="lec5" name="lecturer" value="5" required><label for="lec5">★</label>
            <input type="radio" id="lec4" name="lecturer" value="4"><label for="lec4">★</label>
            <input type="radio" id="lec3" name="lecturer" value="3"><label for="lec3">★</label>
            <input type="radio" id="lec2" name="lecturer" value="2"><label for="lec2">★</label>
            <input type="radio" id="lec1" name="lecturer" value="1"><label for="lec1">★</label>
          </div>
        </div>

        <div class="rating-column">
          <h3>Tutor</h3>
          <div class="stars">
            <input type="radio" id="tut5" name="tutor" value="5" required><label for="tut5">★</label>
            <input type="radio" id="tut4" name="tutor" value="4"><label for="tut4">★</label>
            <input type="radio" id="tut3" name="tutor" value="3"><label for="tut3">★</label>
            <input type="radio" id="tut2" name="tutor" value="2"><label for="tut2">★</label>
            <input type="radio" id="tut1" name="tutor" value="1"><label for="tut1">★</label>
          </div>
        </div>
      </div>

      <div class="comment-section">
        <label for="comment">Comments/Review</label><br>
        <textarea id="comment" name="comment" placeholder="Write your feedback..."></textarea>
      </div>

      <button class="submit-btn" type="submit">Submit</button>
    </form>
  </div>
</body>
</html>
