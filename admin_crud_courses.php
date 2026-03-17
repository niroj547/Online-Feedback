<?php
require_once 'includes/auth_admin.php';
require_once 'includes/helpers.php';

// Add Course
if (isset($_POST['add'])) {
    $name = $_POST['name'];
    $stmt = $conn->prepare("INSERT INTO course (name, status) VALUES (?, 'active')");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $stmt->close();
}

// Suspend / Activate Course
if (isset($_POST['suspend'])) {
    update_entity_status($conn, 'course', intval($_POST['id'] ?? 0), 'suspended');
}
if (isset($_POST['activate'])) {
    update_entity_status($conn, 'course', intval($_POST['id'] ?? 0), 'active');
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
          <td><?= status_badge($row['status']) ?></td>
          <td><?= status_action_form((int)$row['id'], $row['status']) ?></td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>

  <a href="admin_dashboard.php" class="btn btn-secondary mt-3">← Back to Dashboard</a>
</body>
</html>