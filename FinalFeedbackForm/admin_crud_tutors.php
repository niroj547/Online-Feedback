<?php
session_start();
include 'db_config.php';

if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.html");
    exit();
}

// Add Tutor
if (isset($_POST['add'])) {
    $name = trim($_POST['name'] ?? '');
    $course_id = intval($_POST['course_id'] ?? 0);
    
    if (!empty($name) && $course_id > 0) {
        // Check if the course exists
        $stmt = $conn->prepare("SELECT id FROM course WHERE id = ?");
        $stmt->bind_param("i", $course_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $stmt = $conn->prepare("INSERT INTO tutor (name, course_id, status) VALUES (?, ?, 'active')");
            $stmt->bind_param("si", $name, $course_id);
            $stmt->execute();
        } else {
            echo "<script>alert('Course not found.');</script>";
        }
    }
}

// Update Tutor
if (isset($_POST['update'])) {
    $id = intval($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $course_id = intval($_POST['course_id'] ?? 0);
    
    if (!empty($name) && $course_id > 0 && $id > 0) {
        $stmt = $conn->prepare("UPDATE tutor SET name = ?, course_id = ? WHERE id = ?");
        $stmt->bind_param("sii", $name, $course_id, $id);
        $stmt->execute();
    }
}

// Suspend Tutor
if (isset($_POST['suspend'])) {
    $id = intval($_POST['id'] ?? 0);
    if ($id > 0) {
        $stmt = $conn->prepare("UPDATE tutor SET status = 'suspended' WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    }
}

// Activate Tutor
if (isset($_POST['activate'])) {
    $id = intval($_POST['id'] ?? 0);
    if ($id > 0) {
        $stmt = $conn->prepare("UPDATE tutor SET status = 'active' WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    }
}

$result = $conn->query("SELECT * FROM tutor");
?>

<!DOCTYPE html>
<html>
<head>
  <title>Manage Tutors</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
  <h2>Manage Tutors</h2>

  <form method="post" class="mb-3 row g-2">
    <div class="col-md-5">
      <input type="text" name="name" placeholder="Tutor Name" class="form-control" required>
    </div>
    <div class="col-md-4">
      <input type="number" name="course_id" placeholder="Course ID" class="form-control" required>
    </div>
    <div class="col-md-3">
      <button name="add" class="btn btn-success w-100">Add Tutor</button>
    </div>
  </form>

  <table class="table table-bordered">
    <thead>
      <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Course ID</th>
        <th>Status</th>
        <th>Actions</th>
      </tr>
    </thead>
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
              <span class="badge <?= $row['status'] === 'active' ? 'bg-success' : 'bg-warning' ?>">
                <?= ucfirst($row['status']) ?>
              </span>
            </td>
            <td>
              <?php if ($row['status'] === 'active'): ?>
                <button name="suspend" class="btn btn-warning btn-sm me-2">Suspend</button>
              <?php else: ?>
                <button name="activate" class="btn btn-success btn-sm me-2">Activate</button>
              <?php endif; ?>
              <button name="update" class="btn btn-primary btn-sm me-2">Update</button>
            </td>
          </form>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>

  <a href="admin_dashboard.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
</body>
</html>