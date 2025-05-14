<?php
session_start();
include 'db_config.php';

if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.html");
    exit();
}

// Add Course
if (isset($_POST['add'])) {
    $name = $_POST['name'];
    $stmt = $conn->prepare("INSERT INTO course (name, status) VALUES (?, 'active')");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $stmt->close();
}

// Suspend Course
if (isset($_POST['suspend'])) {
    $id = $_POST['id'];
    $stmt = $conn->prepare("UPDATE course SET status = 'suspended' WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

// Activate Course
if (isset($_POST['activate'])) {
    $id = $_POST['id'];
    $stmt = $conn->prepare("UPDATE course SET status = 'active' WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

$result = $conn->query("SELECT * FROM course");
?>

<!DOCTYPE html>
<html>
<head>
  <title>Manage Courses</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
  <h2>Manage Courses</h2>

  <form method="post" class="mb-3 d-flex gap-2">
    <input type="text" name="name" placeholder="Course Name" class="form-control" required>
    <button name="add" class="btn btn-primary">Add</button>
  </form>

  <table class="table table-bordered">
    <thead>
      <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= $row['id'] ?></td>
          <td><?= htmlspecialchars($row['name']) ?></td>
          <td>
            <span class="badge <?= $row['status'] === 'active' ? 'bg-success' : 'bg-warning' ?>">
              <?= ucfirst($row['status']) ?>
            </span>
          </td>
          <td>
            <?php if ($row['status'] === 'active'): ?>
              <form method="post" class="d-inline">
                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                <button name="suspend" class="btn btn-warning btn-sm">Suspend</button>
              </form>
            <?php else: ?>
              <form method="post" class="d-inline">
                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                <button name="activate" class="btn btn-success btn-sm">Activate</button>
              </form>
            <?php endif; ?>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>

  <a href="admin_dashboard.php" class="btn btn-secondary mt-3">← Back to Dashboard</a>
</body>
</html>