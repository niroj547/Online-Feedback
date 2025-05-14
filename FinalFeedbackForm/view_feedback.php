<?php
include 'db_config.php';

// Handle actions
if (isset($_GET['verify_id'])) {
    $conn->query("UPDATE feedback SET verified = 1 WHERE id = " . intval($_GET['verify_id']));
    header("Location: view_feedback.php");
    exit();
}
if (isset($_GET['unverify_id'])) {
    $unverify_id = intval($_GET['unverify_id']);
    $query = "UPDATE feedback SET verified = 0 WHERE id = $unverify_id";
    if ($conn->query($query) === TRUE) {
        header("Location: view_feedback.php");
        exit();
    } else {
        echo "Error updating record: " . $conn->error;
    }
}
if (isset($_GET['delete_id'])) {
    $conn->query("DELETE FROM feedback WHERE id = " . intval($_GET['delete_id']));
    header("Location: view_feedback.php");
    exit();
}

// Handle toggle verification
if (isset($_POST['toggle_verify'])) {
    $id = intval($_POST['id']);
    $new_status = intval($_POST['new_status']); // 1 for verified, 0 for unverified
    $stmt = $conn->prepare("UPDATE feedback SET verified = ? WHERE id = ?");
    $stmt->bind_param("ii", $new_status, $id);
    $stmt->execute();
    $stmt->close();
    header("Location: view_feedback.php");
    exit();
}

// Fetch sorting/filtering parameters
$sort_column = $_GET['sort'] ?? 'feedback_date';
$sort_order = $_GET['order'] ?? 'DESC';
$filter_verified = $_GET['verified'] ?? '';
$filter_course = $_GET['course'] ?? '';
$filter_lecturer = $_GET['lecturer'] ?? '';
$filter_tutor = $_GET['tutor'] ?? '';
$filter_student = $_GET['student'] ?? '';
$filter_stars = $_GET['stars'] ?? '';

// Validate columns
$allowed_columns = ['feedback_date', 'lecturer_rating', 'tutor_rating', 'student_name', 'lecturer_name', 'tutor_name'];
if (!in_array($sort_column, $allowed_columns)) {
    $sort_column = 'feedback_date';
}
$sort_order = strtoupper($sort_order) === 'ASC' ? 'ASC' : 'DESC';

// Fetch dropdown options
$courses = $conn->query("SELECT DISTINCT name FROM course ORDER BY name ASC");
$lecturers = $conn->query("SELECT DISTINCT name FROM lecturer ORDER BY name ASC");
$tutors = $conn->query("SELECT DISTINCT name FROM tutor ORDER BY name ASC");
$students = $conn->query("SELECT DISTINCT student_name FROM feedback WHERE student_name IS NOT NULL ORDER BY student_name ASC");

// Query feedback
$sql = "SELECT f.id, f.feedback_date, f.student_name, f.anonymous_mode, f.verified,
        c.name AS course_name, l.name AS lecturer_name, t.name AS tutor_name,
        f.lecturer_rating, f.tutor_rating
        FROM feedback f
        LEFT JOIN course c ON f.course_id = c.id
        LEFT JOIN lecturer l ON f.lecturer_id = l.id
        LEFT JOIN tutor t ON f.tutor_id = t.id
        WHERE 1=1";

if ($filter_verified !== '') $sql .= " AND f.verified = " . intval($filter_verified);
if ($filter_course !== '') $sql .= " AND c.name = '" . $conn->real_escape_string($filter_course) . "'";
if ($filter_lecturer !== '') $sql .= " AND l.name = '" . $conn->real_escape_string($filter_lecturer) . "'";
if ($filter_tutor !== '') $sql .= " AND t.name = '" . $conn->real_escape_string($filter_tutor) . "'";
if ($filter_student !== '') $sql .= " AND f.student_name = '" . $conn->real_escape_string($filter_student) . "'";
if ($filter_stars !== '') $sql .= " AND (f.lecturer_rating = " . intval($filter_stars) . " OR f.tutor_rating = " . intval($filter_stars) . ")";

$sql .= " ORDER BY $sort_column $sort_order";

$result = $conn->query($sql);

// Fetch single feedback details if `id` is set
$feedback = null;
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $feedback = $conn->query("
        SELECT f.*, c.name AS course_name, l.name AS lecturer_name, t.name AS tutor_name
        FROM feedback f
        LEFT JOIN course c ON f.course_id = c.id
        LEFT JOIN lecturer l ON f.lecturer_id = l.id
        LEFT JOIN tutor t ON f.tutor_id = t.id
        WHERE f.id = $id
    ")->fetch_assoc();
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>View Feedback</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <style>
    .form-switch {
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .form-switch input {
      width: 40px;
      height: 20px;
      appearance: none;
      background: #ccc;
      outline: none;
      border-radius: 20px;
      position: relative;
      cursor: pointer;
      transition: background 0.3s ease-in-out;
    }
    .form-switch input:checked {
      background: #28a745;
    }
    .form-switch input::before {
      content: '';
      position: absolute;
      width: 18px;
      height: 18px;
      background: #fff;
      border-radius: 50%;
      top: 1px;
      left: 1px;
      transition: transform 0.3s ease-in-out;
    }
    .form-switch input:checked::before {
      transform: translateX(20px);
    }
  </style>
</head>
<body class="bg-light">
<div class="container py-4">
    <a href="admin_dashboard.php" class="btn btn-secondary mb-3">Back to Dashboard</a>
    <!-- Existing content -->
    <?php if ($feedback): ?>
  <!-- Single Feedback View -->
  <h2 class="mb-3">Feedback Details</h2>
  <div class="card mb-4">
    <div class="card-header bg-primary text-white">Feedback Report</div>
    <div class="card-body">
      <p><strong>Course:</strong> <?= htmlspecialchars($feedback['course_name'] ?? '') ?></p>
      <p><strong>Student:</strong> <?= $feedback['anonymous_mode'] ? 'Anonymous' : htmlspecialchars($feedback['student_name'] ?? '') ?></p>
      <p><strong>Date:</strong> <?= htmlspecialchars($feedback['feedback_date'] ?? '') ?></p>
      <hr>
      <p><strong>Lecturer:</strong> <?= htmlspecialchars($feedback['lecturer_name'] ?? '') ?></p>
      <p><strong>Lecturer Rating:</strong> <?= str_repeat('★', (int)($feedback['lecturer_rating'] ?? 0)) ?></p>
      <p><strong>Lecturer Feedback:</strong><br><?= nl2br(htmlspecialchars($feedback['lecturer_comment'] ?? 'No comment')) ?></p>
      <hr>
      <p><strong>Tutor:</strong> <?= htmlspecialchars($feedback['tutor_name'] ?? '') ?></p>
      <p><strong>Tutor Rating:</strong> <?= str_repeat('★', (int)($feedback['tutor_rating'] ?? 0)) ?></p>
      <p><strong>Tutor Feedback:</strong><br><?= nl2br(htmlspecialchars($feedback['tutor_comment'] ?? 'No comment')) ?></p>
    </div>
    <div class="card-footer d-flex justify-content-between">
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
  <!-- Feedback Table -->
  <h2 class="mb-4">Feedback Dashboard</h2>

  <!-- Filter Layout -->
  <form method="GET" class="row g-3 mb-4">
    <div class="col-md-3">
      <label for="course" class="form-label">Course</label>
      <select name="course" id="course" class="form-select">
        <option value="">All</option>
        <?php while ($course = $courses->fetch_assoc()): ?>
          <option value="<?= htmlspecialchars($course['name']) ?>" <?= $filter_course === $course['name'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($course['name']) ?>
          </option>
        <?php endwhile; ?>
      </select>
    </div>
    <div class="col-md-3">
      <label for="lecturer" class="form-label">Lecturer</label>
      <select name="lecturer" id="lecturer" class="form-select">
        <option value="">All</option>
        <?php while ($lecturer = $lecturers->fetch_assoc()): ?>
          <option value="<?= htmlspecialchars($lecturer['name']) ?>" <?= $filter_lecturer === $lecturer['name'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($lecturer['name']) ?>
          </option>
        <?php endwhile; ?>
      </select>
    </div>
    <div class="col-md-3">
      <label for="tutor" class="form-label">Tutor</label>
      <select name="tutor" id="tutor" class="form-select">
        <option value="">All</option>
        <?php while ($tutor = $tutors->fetch_assoc()): ?>
          <option value="<?= htmlspecialchars($tutor['name']) ?>" <?= $filter_tutor === $tutor['name'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($tutor['name']) ?>
          </option>
        <?php endwhile; ?>
      </select>
    </div>
    <div class="col-md-3">
      <label for="student" class="form-label">Student</label>
      <select name="student" id="student" class="form-select">
        <option value="">All</option>
        <?php while ($student = $students->fetch_assoc()): ?>
          <option value="<?= htmlspecialchars($student['student_name']) ?>" <?= $filter_student === $student['student_name'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($student['student_name']) ?>
          </option>
        <?php endwhile; ?>
      </select>
    </div>
    <div class="col-md-3">
      <label for="stars" class="form-label">Stars</label>
      <select name="stars" id="stars" class="form-select">
        <option value="">All</option>
        <option value="1" <?= $filter_stars === '1' ? 'selected' : '' ?>>1 Star</option>
        <option value="2" <?= $filter_stars === '2' ? 'selected' : '' ?>>2 Stars</option>
        <option value="3" <?= $filter_stars === '3' ? 'selected' : '' ?>>3 Stars</option>
        <option value="4" <?= $filter_stars === '4' ? 'selected' : '' ?>>4 Stars</option>
        <option value="5" <?= $filter_stars === '5' ? 'selected' : '' ?>>5 Stars</option>
      </select>
    </div>
    <div class="col-md-3">
      <label for="verified" class="form-label">Verified</label>
      <select name="verified" id="verified" class="form-select">
        <option value="">All</option>
        <option value="1" <?= $filter_verified === '1' ? 'selected' : '' ?>>Verified</option>
        <option value="0" <?= $filter_verified === '0' ? 'selected' : '' ?>>Not Verified</option>
      </select>
    </div>
    <div class="col-md-3">
      <label for="sort" class="form-label">Sort By</label>
      <select name="sort" id="sort" class="form-select">
        <option value="feedback_date" <?= $sort_column === 'feedback_date' ? 'selected' : '' ?>>Date</option>
        <option value="lecturer_rating" <?= $sort_column === 'lecturer_rating' ? 'selected' : '' ?>>Lecturer Rating</option>
        <option value="tutor_rating" <?= $sort_column === 'tutor_rating' ? 'selected' : '' ?>>Tutor Rating</option>
        <option value="student_name" <?= $sort_column === 'student_name' ? 'selected' : '' ?>>Student Name</option>
        <option value="lecturer_name" <?= $sort_column === 'lecturer_name' ? 'selected' : '' ?>>Lecturer Name</option>
        <option value="tutor_name" <?= $sort_column === 'tutor_name' ? 'selected' : '' ?>>Tutor Name</option>
      </select>
    </div>
    <div class="col-md-3">
      <label for="order" class="form-label">Order</label>
      <select name="order" id="order" class="form-select">
        <option value="ASC" <?= $sort_order === 'ASC' ? 'selected' : '' ?>>Ascending</option>
        <option value="DESC" <?= $sort_order === 'DESC' ? 'selected' : '' ?>>Descending</option>
      </select>
    </div>
    <div class="col-md-12">
      <button type="submit" class="btn btn-primary">Apply Filters</button>
      <a href="view_feedback.php" class="btn btn-secondary">Reset</a>
    </div>
  </form>

  <!-- Feedback Table -->
  <div class="table-responsive">
    <table class="table table-bordered bg-white">
      <thead class="table-dark">
        <tr>
          <th>Date</th>
          <th>Course</th>
          <th>Student</th>
          <th>Lecturer</th>
          <th>Tutor</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
      <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($row['feedback_date'] ?? '') ?></td>
          <td><?= htmlspecialchars($row['course_name'] ?? '') ?></td>
          <td><?= $row['anonymous_mode'] ? '<i>Anonymous</i>' : htmlspecialchars($row['student_name'] ?? '') ?></td>
          <td><?= htmlspecialchars($row['lecturer_name'] ?? '') ?></td>
          <td><?= htmlspecialchars($row['tutor_name'] ?? '') ?></td>
          <td>
            <span class="badge <?= $row['verified'] ? 'bg-success' : 'bg-danger' ?>">
              <?= $row['verified'] ? 'Verified' : 'Unverified' ?>
            </span>
          </td>
          <td>
            <form method="post" class="d-inline">
              <input type="hidden" name="id" value="<?= $row['id'] ?>">
              <input type="hidden" name="new_status" value="<?= $row['verified'] ? 0 : 1 ?>">
              <button type="submit" name="toggle_verify" class="btn <?= $row['verified'] ? 'btn-warning' : 'btn-success' ?> btn-sm">
                <?= $row['verified'] ? 'Unverify' : 'Verify' ?>
              </button>
            </form>
            <a href="?id=<?= $row['id'] ?>" class="btn btn-sm btn-info">View</a>
            <a href="?delete_id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
          </td>
        </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="7" class="text-center text-muted">No feedback found.</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>

</div>
</body>
</html>