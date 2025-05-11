<?php
session_start();
if (!isset($_SESSION['student'])) {
    header("Location: student_login.html");
    exit();
}

include 'db_config.php';

// Get email from session
$email = $_SESSION['student'];

// Extract student name from email
$name_parts = explode('.', explode('@', $email)[0]);
$student_name = ucfirst($name_parts[0]) . ' ' . ucfirst($name_parts[1]);

// Fetch student details from DB including last_feedback_time
$stmt = $conn->prepare("SELECT academic_year, semester, section, program FROM students WHERE email = ? AND status = 'active'");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$student_data = $result->fetch_assoc();
$stmt->close();

if (!$student_data) {
    die("Error: Student data not found or account is suspended.");
}

// Check if feedback was submitted within last 24 hours
$can_submit = true;
$last_feedback_time = $student_data['last_feedback_time'];

if (!empty($last_feedback_time)) {
    $last_time = strtotime($last_feedback_time);
    $current_time = time();
    if (($current_time - $last_time) < 86400) { // 24 hours = 86400 seconds
        $can_submit = false;
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && $can_submit) {
    // Update last_feedback_time and redirect
    $now = date('Y-m-d H:i:s');
    $update = $conn->prepare("UPDATE students SET last_feedback_time = ? WHERE email = ?");
    $update->bind_param("ss", $now, $email);
    $update->execute();
    $update->close();

    header("Location: course_select.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Student Feedback Form</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: linear-gradient(135deg, #a3e3d3, #f8e1db);
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      margin: 0;
    }

    .form-container {
      background-color: #fff;
      padding: 40px;
      border-radius: 15px;
      box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
      max-width: 600px;
      width: 100%;
    }

    .form-container h1 {
      font-size: 28px;
      color: #007b7f;
      text-align: center;
      margin-bottom: 20px;
    }

    .form-container p {
      font-size: 14px;
      color: #555;
      text-align: center;
      margin-bottom: 30px;
    }

    .form-group label {
      font-weight: bold;
      color: #333;
    }

    .form-control {
      border-radius: 8px;
      padding: 10px;
    }

    .btn-primary {
      background-color: #007b7f;
      border: none;
      padding: 12px 20px;
      border-radius: 10px;
      font-size: 16px;
      cursor: pointer;
      width: 100%;
    }

    .btn-primary:hover {
      background-color: #005a5d;
    }

    .alert {
      margin-top: 20px;
    }
  </style>
</head>
<body>
  <div class="form-container">
    <h1>Student Feedback Form</h1>
    <p>Fill out the feedback form to help us improve our teaching quality.</p>

    <form method="POST" action="">
      <div class="form-group mb-3">
        <label for="studentName">Your Name</label>
        <input type="text" name="studentName" id="studentName" class="form-control" value="<?= htmlspecialchars($student_name) ?>" readonly>
      </div>

      <div class="form-check mb-3">
        <input type="checkbox" class="form-check-input" id="anonymous" name="anonymous">
        <label class="form-check-label" for="anonymous">Submit anonymously</label>
      </div>

      <div class="form-group mb-3">
        <label for="academicYear">Academic Year</label>
        <input type="text" name="academicYear" id="academicYear" class="form-control" value="<?= htmlspecialchars($student_data['academic_year']) ?>" readonly>
      </div>

      <div class="form-group mb-3">
        <label for="semester">Semester</label>
        <input type="text" name="semester" id="semester" class="form-control" value="<?= htmlspecialchars($student_data['semester']) ?>" readonly>
      </div>

      <div class="form-group mb-3">
        <label for="section">Section</label>
        <input type="text" name="section" id="section" class="form-control" value="<?= htmlspecialchars($student_data['section']) ?>" readonly>
      </div>

      <div class="form-group mb-3">
        <label for="program">Program</label>
        <input type="text" name="program" id="program" class="form-control" value="<?= htmlspecialchars($student_data['program']) ?>" readonly>
      </div>

      <div class="form-group mb-3">
        <label for="feedbackDate">Date</label>
        <input type="date" name="feedbackDate" id="feedbackDate" class="form-control" value="<?= date('Y-m-d') ?>" readonly>
      </div>

      <?php if ($can_submit): ?>
        <button type="submit" class="btn-primary">Get Started</button>
      <?php else: ?>
        <div class="alert alert-warning text-center">
          You have already submitted feedback in the last 24 hours.<br>
          Please try again later.
        </div>
      <?php endif; ?>
    </form>
  </div>
</body>
</html>
