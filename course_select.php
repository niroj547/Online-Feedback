<?php
include 'db_config.php';

// Fetch course list
$courses_query = $conn->query("SELECT id, name FROM course WHERE status = 'active' ORDER BY name ASC");

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
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      font-family: 'Segoe UI', sans-serif;
    }

    body {
      height: 100vh;
      background: linear-gradient(135deg, #a3e3d3, #f8e1db);
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .container {
      width: 1000px;
      max-width: 95%; /* Improved responsiveness */
      height: auto; /* Adjust height based on content */
      max-height: 90%;
      display: flex;
      box-shadow: 0 0 25px rgba(0,0,0,0.15); /* Slightly stronger shadow */
      border-radius: 20px;
      overflow: hidden;
      background-color: #fff;
    }

    .left-panel {
      flex: 0 0 35%; /* Fixed width for the left panel */
      background-image: url('image/logo.jpg'); /* Keep your logo or change */
      background-size: 40%; /* Slightly larger logo */
      background-repeat: no-repeat;
      background-position: center;
      background-color: #f0f8f8;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      padding: 40px; /* Adjusted padding */
    }

    .left-panel h2 {
      font-size: 36px; /* More prominent title */
      font-weight: bold;
      color: #007b7f; /* Primary color for the title */
      margin-top: auto;
      text-align: center;
      letter-spacing: 1px;
    }

    .right-panel {
      flex: 1;
      background-color: #fff;
      color: #333;
      display: flex;
      flex-direction: column;
      justify-content: flex-start; /* Align items from the top */
      padding: 40px;
      border-left: 1px solid #eee; /* Subtle separation */
    }

    .right-panel h2 {
      font-size: 32px;
      margin-bottom: 30px;
      color: #007b7f;
      text-align: center;
    }

    .form-check {
      margin-bottom: 18px; /* Slightly more spacing */
      padding-left: 2.5em;
      transition: background-color 0.15s ease-in-out; /* Hover effect */
      border-radius: 5px;
      padding: 10px 15px;
    }

    .form-check:hover {
      background-color: #f9f9f9;
    }

    .form-check-input {
      float: left;
      margin-left: -1.5em;
      margin-top: 0.3em; /* Adjust vertical alignment */
    }

    .form-check-label {
      font-size: 18px;
      color: #555;
      cursor: pointer; /* Indicate it's selectable */
    }

    .btn-primary {
      background-color: #007b7f;
      color: white;
      border: none;
      padding: 14px 30px;
      border-radius: 10px;
      font-size: 18px;
      cursor: pointer;
      margin-top: 40px; /* More top spacing */
      width: auto;
      display: block;
      margin-left: auto;
      margin-right: auto;
      transition: background-color 0.2s ease-in-out, transform 0.1s ease; /* Hover animation */
    }

    .btn-primary:hover {
      background-color: #005a5d;
      transform: translateY(-2px);
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
      .container {
        flex-direction: column; /* Stack panels on smaller screens */
        width: 90%;
        height: auto;
      }

      .left-panel {
        flex: 0 0 auto;
        padding: 30px;
        text-align: center;
      }

      .right-panel {
        padding: 30px;
        border-left: none;
      }

      .left-panel h2 {
        margin-top: 20px;
      }
    }
  </style>
</head>
<body>
<div class="container">
  <div class="left-panel">
    <h2>Select Course</h2>
  </div>
  <div class="right-panel">
    <h2>Choose a Course to Rate</h2>
    <form action="rating_form.php" method="post">
      <?php foreach ($courses as $course): ?>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="course_id" id="course_<?= $course['id'] ?>" value="<?= $course['id'] ?>" required>
          <label class="form-check-label" for="course_<?= $course['id'] ?>">
            <?= htmlspecialchars($course['name']) ?>
          </label>
        </div>
      <?php endforeach; ?>

      <button type="submit" class="btn btn-primary mt-4">Next</button>
    </form>
  </div>
</div>
</body>
</html>