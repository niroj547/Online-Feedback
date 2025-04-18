<?php
session_start();
include 'db_config.php';

if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.html");
    exit();
}

// Add Lecturer
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
            $stmt = $conn->prepare("INSERT INTO lecturer (name, course_id) VALUES (?, ?)");
            $stmt->bind_param("si", $name, $course_id);
            $stmt->execute();
        } else {
            // Course does not exist
            echo "<script>alert('Course not found.');</script>";
        }
    }
}

// Update Lecturer
if (isset($_POST['update'])) {
    $id = intval($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $course_id = intval($_POST['course_id'] ?? 0);
    
    if (!empty($name) && $course_id > 0 && $id > 0) {
        // Check if the course exists
        $stmt = $conn->prepare("SELECT id FROM course WHERE id = ?");
        $stmt->bind_param("i", $course_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $stmt = $conn->prepare("UPDATE lecturer SET name = ?, course_id = ? WHERE id = ?");
            $stmt->bind_param("sii", $name, $course_id, $id);
            $stmt->execute();
        } else {
            echo "<script>alert('Course not found.');</script>";
        }
    }
}

// Delete Lecturer
if (isset($_POST['delete'])) {
    $id = intval($_POST['id'] ?? 0);
    if ($id > 0) {
        $stmt = $conn->prepare("DELETE FROM lecturer WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    }
}

$result = $conn->query("SELECT * FROM lecturer");
?>
<!DOCTYPE html>
<html>
<head>
  <title>Manage Lecturers</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      padding: 40px;
      background-color: #f8f9fa;
    }

    h2 {
      margin-bottom: 20px;
    }

    .btn-update {
      background-color: white;
      color: #0d6efd;
      border: 1px solid #0d6efd;
    }

    .btn-update:hover {
      background-color: #0d6efd;
      color: white;
    }

    .btn-delete {
      background-color: white;
      color: #dc3545;
      border: 1px solid #dc3545;
    }

    .btn-delete:hover {
      background-color: #dc3545;
      color: white;
    }

    .btn-back {
      background-color: #e0e0e0;
      border: none;
      width: 100%;
      padding: 10px;
      margin-top: 30px;
      text-align: center;
      font-weight: 500;
    }

    .btn-back:hover {
      background-color: #d5d5d5;
    }

    table td:first-child,
    table td:nth-child(2),
    table td:nth-child(3),
    table th:first-child,
    table th:nth-child(2),
    table th:nth-child(3) {
      text-align: left;
    }

    td, th {
      vertical-align: middle !important;
    }
  </style>
</head>
<body>
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

  <table class="table table-bordered bg-white align-middle">
    <thead class="table-light">
      <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Course ID</th>
        <th class="text-center">Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= $row['id'] ?></td>
          <td><?= htmlspecialchars($row['name']) ?></td>
          <td><?= $row['course_id'] ?></td>
          <td>
            <form method="post" class="d-flex justify-content-center gap-2">
              <input type="hidden" name="id" value="<?= $row['id'] ?>">
              <input type="hidden" name="name" value="<?= htmlspecialchars($row['name']) ?>">
              <input type="hidden" name="course_id" value="<?= $row['course_id'] ?>">
              <button name="update" class="btn btn-update btn-sm">Update</button>
              <button name="delete" class="btn btn-delete btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
            </form>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>

  <a href="admin_dashboard.php" class="btn btn-back">← Back to Dashboard</a>
</body>
</html>
