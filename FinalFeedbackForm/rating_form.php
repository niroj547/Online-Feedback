<?php
include 'db_config.php';
session_start();

if (!isset($_SESSION['student'])) {
    header("Location: student_login.html");
    exit();
}

// Initialize variables
$showSuccessModal = false; // Ensure this variable is always defined
$showErrorModal = false;   // Initialize this variable to avoid undefined variable warnings
$success = $error = '';
$course_name = '';
$lecturer_name = '';
$tutor_name = '';

// Extract the name from the email pattern (e.g., name.caste.edu.np)
$email = $_SESSION['student'];
$name_parts = explode('.', explode('@', $email)[0]);
$student_name = ucfirst($name_parts[0]) . ' ' . ucfirst($name_parts[1]);

// Check if the form is submitted anonymously
$is_anonymous = isset($_POST['anonymous']) && $_POST['anonymous'] == '1';
if ($is_anonymous) {
    $student_name = 'Anonymous';
}

$course_id = intval($_POST['course_id'] ?? 0);

if ($course_id > 0) {
    $stmt = $conn->prepare("SELECT name FROM course WHERE id = ? AND status = 'active'");
    $stmt->bind_param("i", $course_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $course_name = $result->fetch_assoc()['name'] ?? '';
    $stmt->close();

    $lecturer_result = $conn->query("SELECT name FROM lecturer WHERE id = $course_id AND status = 'active'");
    $lecturer_name = $lecturer_result->num_rows > 0 ? $lecturer_result->fetch_assoc()['name'] : 'Not assigned';

    $tutor_result = $conn->query("SELECT name FROM tutor WHERE id = $course_id AND status = 'active'");
    $tutor_name = $tutor_result->num_rows > 0 ? $tutor_result->fetch_assoc()['name'] : 'Not assigned';
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['submit'])) {
    $student_name = $is_anonymous ? 'Anonymous' : trim($_POST['student_name'] ?? '');
    $lecturer_id = $course_id;
    $tutor_id = $course_id;
    $lecturer_rating = intval($_POST['lecturer'] ?? 0);
    $tutor_rating = intval($_POST['tutor'] ?? 0);
    $lecturer_comment = trim($_POST['lecturer_comment'] ?? '');
    $tutor_comment = trim($_POST['tutor_comment'] ?? '');

    $allow_submit = true;

    if (!$is_anonymous && $student_name === '') {
        $error = "Please enter your name or select anonymous.";
        $showErrorModal = true;
    } else {
        if (!$is_anonymous) {
            $one_day_ago = date('Y-m-d H:i:s', strtotime('-24 hours'));
            $check_sql = $conn->prepare("SELECT id FROM feedback WHERE student_name = ? AND course_id = ? AND feedback_date >= ?");
            $check_sql->bind_param("sis", $student_name, $course_id, $one_day_ago);
            $check_sql->execute();
            $check_result = $check_sql->get_result();

            if ($check_result->num_rows > 0) {
                $error = "You have already submitted feedback for this course in the last 24 hours.";
                $allow_submit = false;
                $showErrorModal = true;
            }

            $check_sql->close();
        }

        if ($allow_submit && $lecturer_rating && $tutor_rating) {
            $stmt = $conn->prepare("INSERT INTO feedback
                (student_name, course_id, lecturer_id, tutor_id, lecturer_rating, tutor_rating, lecturer_comment, tutor_comment, anonymous_mode, verified, feedback_date)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 0, NOW())");

            $stmt->bind_param("siiiisssi", $student_name, $course_id, $lecturer_id, $tutor_id, $lecturer_rating, $tutor_rating, $lecturer_comment, $tutor_comment, $is_anonymous);

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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Feedback Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            height: 100vh;
            background: linear-gradient(135deg, #a3e3d3, #f8e1db);
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .container {
            width: 1000px;
            max-width: 95%;
            height: auto;
            display: flex;
            box-shadow: 0 0 25px rgba(0, 0, 0, 0.15);
            border-radius: 20px;
            overflow: visible;
            background-color: #fff;
        }

        .left-panel {
            flex: 0 0 35%;
            background-image: url('image/logo.jpg');
            background-size: 40%;
            background-repeat: no-repeat;
            background-position: center;
            background-color: #f0f8f8;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 40px;
        }

        .left-panel h2 {
            font-size: 36px;
            font-weight: bold;
            color: #007b7f;
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
            justify-content: flex-start;
            padding: 40px;
            border-left: 1px solid #eee;
        }

        .right-panel h2 {
            font-size: 32px;
            margin-bottom: 30px;
            color: #007b7f;
            text-align: center;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            font-size: 18px;
            color: #555;
            font-weight: bold;
        }

        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 8px;
            background-color: #f9f9f9;
        }

        .form-control::placeholder {
            color: #aaa;
        }

        .star-rating {
            display: flex;
            flex-direction: row-reverse;
            justify-content: flex-start;
            margin-top: 10px;
        }

        .star-rating input[type="radio"] {
            display: none;
        }

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

        .form-check {
            margin-bottom: 15px;
            padding-left: 1.5em;
        }

        .form-check-input {
            float: left;
            margin-left: -1.5em;
        }

        .form-check-label {
            font-size: 16px;
            color: #555;
        }

        .btn-primary {
            background-color: #007b7f;
            color: white;
            border: none;
            padding: 14px 30px;
            border-radius: 10px;
            font-size: 18px;
            cursor: pointer;
            margin-top: 20px;
            width: auto;
            display: block;
            margin-left: auto;
            margin-right: auto;
            transition: background-color 0.2s ease-in-out, transform 0.1s ease;
        }

        .btn-primary:hover {
            background-color: #005a5d;
            transform: translateY(-2px);
        }

        .popup {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #fff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 0 25px rgba(0, 0, 0, 0.2);
            text-align: center;
            z-index: 1000;
            display: none;
        }

        .popup h2 {
            color: #007b7f;
            margin-bottom: 15px;
        }

        .popup p {
            color: #555;
            margin-bottom: 20px;
        }

        .popup button {
            background-color: #007b7f;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 10px;
            cursor: pointer;
        }

        .popup button:hover {
            background-color: #005a5d;
        }

        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 999;
            display: none;
        }

        .error-popup {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #f8d7da;
            color: #721c24;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 0 25px rgba(0, 0, 0, 0.2);
            text-align: center;
            z-index: 1000;
            display: none;
        }

        .error-popup h2 {
            color: #721c24;
            margin-bottom: 15px;
        }

        .error-popup p {
            color: #721c24;
            margin-bottom: 20px;
        }

        .error-popup button {
            background-color: #f5c6cb;
            color: #721c24;
            border: none;
            padding: 10px 20px;
            border-radius: 10px;
            cursor: pointer;
        }

        .error-popup button:hover {
            background-color: #f1b0b7;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="left-panel">
        <h2>Feedback Form</h2>
    </div>
    <div class="right-panel">
        <h2>Provide Your Feedback</h2>
        <form method="post" id="feedbackForm">
            <input type="hidden" name="course_id" value="<?= $course_id ?>">
            <div class="form-group">
                <label for="student_name">Your Name</label>
                <input type="text" name="student_name" id="student_name" class="form-control" value="<?= htmlspecialchars($student_name) ?>" readonly>
            </div>
            <div class="form-check form-group">
                <input type="checkbox" class="form-check-input" id="anonymous" name="anonymous" value="1" <?= $is_anonymous ? 'checked' : '' ?> onchange="toggleAnonymous()">
                <label class="form-check-label" for="anonymous">Submit anonymously</label>
            </div>
            <div class="form-group">
                <label><strong>Lecturer:</strong> <?= htmlspecialchars($lecturer_name) ?></label>
                <div class="star-rating">
                    <?php for ($i = 5; $i >= 1; $i--): ?>
                        <input type="radio" id="lecturer<?= $i ?>" name="lecturer" value="<?= $i ?>" required>
                        <label for="lecturer<?= $i ?>">★</label>
                    <?php endfor; ?>
                </div>
            </div>
            <div class="form-group">
                <label for="lecturer_comment">Lecturer Feedback (Optional)</label>
                <textarea name="lecturer_comment" id="lecturer_comment" class="form-control" rows="3" placeholder="Your feedback for the lecturer"></textarea>
            </div>
            <div class="form-group">
                <label><strong>Tutor:</strong> <?= htmlspecialchars($tutor_name) ?></label>
                <div class="star-rating">
                    <?php for ($i = 5; $i >= 1; $i--): ?>
                        <input type="radio" id="tutor<?= $i ?>" name="tutor" value="<?= $i ?>" required>
                        <label for="tutor<?= $i ?>">★</label>
                    <?php endfor; ?>
                </div>
            </div>
            <div class="form-group">
                <label for="tutor_comment">Tutor Feedback (Optional)</label>
                <textarea name="tutor_comment" id="tutor_comment" class="form-control" rows="3" placeholder="Your feedback for the tutor"></textarea>
            </div>
            <button type="submit" name="submit" class="btn-primary">Submit Feedback</button>
        </form>
    </div>
</div>
<?php if ($showSuccessModal): ?>
    <div class="overlay"></div>
    <div class="popup">
        <h2>Feedback Submitted Successfully</h2>
        <p>Thank you for your feedback! Your input helps us improve.</p>
        <button onclick="window.location.href='course_select.php'">Rate Another Subject</button>
    </div>
<?php elseif ($showErrorModal): ?>
    <div class="overlay"></div>
    <div class="error-popup">
        <h2>Feedback Submission Restricted</h2>
        <p><?= htmlspecialchars($error) ?></p>
        <button onclick="document.querySelector('.overlay').style.display='none'; document.querySelector('.error-popup').style.display='none';">Close</button>
    </div>
<?php endif; ?>
<script>
    function toggleAnonymous() {
        const anon = document.getElementById("anonymous").checked;
        const nameField = document.getElementById("student_name");
        if (anon) {
            nameField.value = 'Anonymous';
        } else {
            nameField.value = '<?= htmlspecialchars($student_name) ?>';
        }
    }

    window.onload = function () {
        toggleAnonymous();
    };

    <?php if ($showSuccessModal || $showErrorModal): ?>
        document.querySelector('.overlay').style.display = 'block';
        <?php if ($showErrorModal): ?>
            document.querySelector('.error-popup').style.display = 'block';
        <?php else: ?>
            document.querySelector('.popup').style.display = 'block';
        <?php endif; ?>
    <?php endif; ?>
</script>
</body>
</html>