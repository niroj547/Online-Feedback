<?php
include 'db_config.php';

// Get the course ID from the URL parameter
$course_id = intval($_GET['course_id'] ?? 0);
$course_name = '';
$lecturer_name = '';
$tutor_name = '';

if ($course_id > 0) {
    // Fetch course details
    $stmt = $conn->prepare("SELECT name FROM course WHERE id = ?");
    $stmt->bind_param("i", $course_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $course = $result->fetch_assoc();
        $course_name = $course['name'];
        
        // Fetch lecturer details for the course
        $stmt_lecturer = $conn->prepare("SELECT name FROM lecturer WHERE course_id = ? LIMIT 1");
        $stmt_lecturer->bind_param("i", $course_id);
        $stmt_lecturer->execute();
        $result_lecturer = $stmt_lecturer->get_result();
        
        if ($result_lecturer->num_rows > 0) {
            $lecturer = $result_lecturer->fetch_assoc();
            $lecturer_name = $lecturer['name'];
        }
        
        // Fetch tutor details for the course
        $stmt_tutor = $conn->prepare("SELECT name FROM tutor WHERE course_id = ? LIMIT 1");
        $stmt_tutor->bind_param("i", $course_id);
        $stmt_tutor->execute();
        $result_tutor = $stmt_tutor->get_result();
        
        if ($result_tutor->num_rows > 0) {
            $tutor = $result_tutor->fetch_assoc();
            $tutor_name = $tutor['name'];
        }
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Rate <?= htmlspecialchars($course_name) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background-image: url('background.jpg');
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
      align-items: center;
      margin: 5px 0;
    }

    .stars label {
      margin-right: 8px;
      width: 60px;
    }

    .star-input {
      direction: rtl;
      unicode-bidi: bidi-override;
    }

    .star-input input {
      display: none;
    }

    .star-input label {
      font-size: 20px;
      color: #fff;
      cursor: pointer;
    }

    .star-input label:hover,
    .star-input label:hover ~ label,
    .star-input input:checked ~ label {
      color: #ffc107;
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

    .next-btn {
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
      margin-top: 30px;
    }

    .success-message {
      background-color: rgba(0, 128, 0, 0.1);
      color: green;
      padding: 15px;
      border-radius: 10px;
      margin-top: 20px;
      text-align: center;
    }

    .warning-message {
      background-color: rgba(255, 165, 0, 0.1);
      color: orange;
      padding: 15px;
      border-radius: 10px;
      margin-top: 20px;
      text-align: center;
    }

    .popup {
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      background-color: rgba(0, 0, 0, 0.9);
      padding: 30px;
      border-radius: 15px;
      color: white;
      text-align: center;
      display: none;
      z-index: 1000;
    }

    .popup button {
      margin-top: 20px;
      padding: 10px 20px;
      background-color: #d8ccc8;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }
  </style>
</head>
<body>
  <div class="rating-container">
    <h2>Student Feedback Form</h2>
    <p><strong>Course:</strong> <?= htmlspecialchars($course_name) ?></p>
    <p><strong>Lecturer:</strong> <?= htmlspecialchars($lecturer_name) ?></p>
    <p><strong>Tutor:</strong> <?= htmlspecialchars($tutor_name) ?></p>

    <form id="feedbackForm" action="submit_feedback.php" method="post">
      <input type="hidden" name="course_id" value="<?= $course_id ?>">
      <div class="rating-section">
        <div class="rating-column">
          <h3>Lecturer</h3>
          <div class="stars"><label>Star</label>
            <div class="star-input">
              <input type="radio" id="lec5" name="lecturer" value="5"><label for="lec5">★</label>
              <input type="radio" id="lec4" name="lecturer" value="4"><label for="lec4">★</label>
              <input type="radio" id="lec3" name="lecturer" value="3"><label for="lec3">★</label>
              <input type="radio" id="lec2" name="lecturer" value="2"><label for="lec2">★</label>
              <input type="radio" id="lec1" name="lecturer" value="1"><label for="lec1">★</label>
            </div>
          </div>
        </div>

        <div class="rating-column">
          <h3>Tutor</h3>
          <div class="stars"><label>Star</label>
            <div class="star-input">
              <input type="radio" id="tut5" name="tutor" value="5"><label for="tut5">★</label>
              <input type="radio" id="tut4" name="tutor" value="4"><label for="tut4">★</label>
              <input type="radio" id="tut3" name="tutor" value="3"><label for="tut3">★</label>
              <input type="radio" id="tut2" name="tutor" value="2"><label for="tut2">★</label>
              <input type="radio" id="tut1" name="tutor" value="1"><label for="tut1">★</label>
            </div>
          </div>
        </div>
      </div>

      <div class="comment-section">
        <label for="comment">Comments/Review</label><br>
        <textarea id="comment" name="comment" placeholder="Write your feedback..."></textarea>
      </div>

      <button class="submit-btn" type="submit">Submit</button>
    </form>

    <div id="successMessage" class="success-message" style="display: none;">
      <i class="bi bi-check-circle"></i> Thank you for your feedback!
    </div>

    <div id="warningMessage" class="warning-message" style="display: none;">
      <i class="bi bi-exclamation-triangle"></i> You have already submitted feedback for this course within the last week.
    </div>
  </div>

  <div id="nextCoursePopup" class="popup">
    <h3>Feedback Submitted!</h3>
    <p>Congratulations on completing the feedback for <?= htmlspecialchars($course_name) ?>!</p>
    <button id="nextCourseBtn">Next Course</button>
  </div>

  <script>
    // Check if feedback already submitted
    document.getElementById('feedbackForm').addEventListener('submit', function(event) {
      const courseId = <?= $course_id ?>;
      const lastFeedback = localStorage.getItem(`feedback_${courseId}`);
      
      if (lastFeedback) {
        const feedbackDate = new Date(lastFeedback);
        const now = new Date();
        const diffTime = now - feedbackDate;
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        
        if (diffDays < 7) {
          event.preventDefault();
          document.getElementById('warningMessage').style.display = 'block';
          setTimeout(() => {
            document.getElementById('warningMessage').style.display = 'none';
          }, 5000);
        }
      }
    });

    // Show success message and popup after form submission
    document.getElementById('feedbackForm').addEventListener('submit', function() {
      const courseId = <?= $course_id ?>;
      localStorage.setItem(`feedback_${courseId}`, new Date().toISOString());
      
      // Show success message
      document.getElementById('successMessage').style.display = 'block';
      setTimeout(() => {
        document.getElementById('successMessage').style.display = 'none';
      }, 3000);
      
      // Show popup after a delay
      setTimeout(() => {
        document.getElementById('nextCoursePopup').style.display = 'block';
      }, 3500);
    });

    // Handle next course button click
    document.getElementById('nextCourseBtn').addEventListener('click', function() {
      window.location.href = 'course_select.php';
    });
  </script>
</body>
</html>