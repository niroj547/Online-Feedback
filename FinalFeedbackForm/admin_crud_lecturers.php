<?php
session_start();
include 'db_config.php';

if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.html");
    exit();
}

// Add Lecturer
if (isset($_POST['add'])) {
    $name = trim($_POST['name']);
    $course_id = intval($_POST['course_id']);
    if (!empty($name) && $course_id > 0) {
        $stmt = $conn->prepare("INSERT INTO lecturer (name, course_id) VALUES (?, ?)");
        $stmt->bind_param("si", $name, $course_id);
        $stmt->execute();
    }
}

// Update Lecturer
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $name = trim($_POST['name']);
    $course_id = intval($_POST['course_id']);
    if (!empty($name) && $course_id > 0) {
        $stmt = $conn->prepare("UPDATE lecturer SET name=?, course_id=? WHERE id=?");
        $stmt->bind_param("sii", $name, $course_id, $id);
        $stmt->execute();
    }
}

// Delete Lecturer
if (isset($_POST['delete'])) {
    $id = $_POST['id'];
    $stmt = $conn->prepare("DELETE FROM lecturer WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

$result = $conn->query("SELECT * FROM lecturer");
?>

<!DOCTYPE html>
<html>
<head>
  <title>Manage Lecturers</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
  <h2>Manage Lecturers</h2>

  <form method="post" class="mb-3 row g-2">
    <div class="col-md-5">
      <input type="text" name="name" placeholder="Lecturer Name" class="form-control" required>
    </div>
    <div class="col-md-4">
      <input type="number" name="course_id" placeholder="Course ID" class="form-control" required>
    </div>
    <div class="col-md-3">
      <button name="add" class="btn btn-success w-100">Add Lecturer</button>
    </div>
  </form>

  <table class="table table-bordered">
    <thead><tr><th>ID</th><th>Name</th><th>Course ID</th><th>Actions</th></tr></thead>
    <tbody>
      <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <form method="post" class="d-flex">
            <td><?= $row['id'] ?></td>
            <td>
              <input type="text" name="name" value="<?= htmlspecialchars($row['name']) ?>" class="form-control" required>
            </td>
            <td>
              <input type="number" name="course_id" value="<?= $row['course_id'] ?>" class="form-control" required>
              <input type="hidden" name="id" value="<?= $row['id'] ?>">
            </td>
            <td>
              <button name="update" class="btn btn-primary btn-sm me-2">Update</button>
              <button name="delete" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
            </td>
          </form>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>

  <a href="admin_dashboard.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
</body>
</html>
