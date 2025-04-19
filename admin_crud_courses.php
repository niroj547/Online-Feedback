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
    $stmt = $conn->prepare("INSERT INTO course (name) VALUES (?)");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $stmt->close();
}

// Delete Course
if (isset($_POST['delete'])) {
    $id = $_POST['id'];
    $stmt = $conn->prepare("DELETE FROM course WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    $conn->query("ALTER TABLE course AUTO_INCREMENT = 1");
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
    <thead><tr><th>ID</th><th>Name</th><th>Action</th></tr></thead>
    <tbody>
      <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= $row['id'] ?></td>
          <td><?= htmlspecialchars($row['name']) ?></td>
          <td>
            <form method="post" class="d-inline">
              <input type="hidden" name="id" value="<?= $row['id'] ?>">
              <button name="delete" class="btn btn-danger btn-sm">Delete</button>
            </form>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>

  <a href="admin_dashboard.php" class="btn btn-secondary mt-3">← Back to Dashboard</a>
</body>
</html>