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
  <style>
    body {
      padding: 40px;
      background-color: #f8f9fa;
    }

    h2 {
      margin-bottom: 20px;
    }

    .form-inline {
      display: flex;
      gap: 10px;
      margin-bottom: 20px;
    }

    .btn-add {
      background-color: #138d75;
      color: white;
    }

    .btn-add:hover {
      background-color: #117a65;
    }

    table th, table td {
      vertical-align: middle;
    }

    table td:nth-child(2) {
      text-align: left;
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

    .btn-delete {
      background-color: white;
      color: #dc3545;
      border: 1px solid #dc3545;
    }

    .btn-delete:hover {
      background-color: #dc3545;
      color: white;
    }
  </style>
</head>
<body>
  <h2>Manage Courses</h2>

  <form method="post" class="form-inline">
    <input type="text" name="name" placeholder="Course Name" class="form-control" required>
    <button name="add" class="btn btn-add">Add</button>
  </form>

  <table class="table table-bordered text-center bg-white">
    <thead class="table-light">
      <tr>
        <th style="width: 10%;">ID</th>
        <th style="width: 70%;" class="text-start">Name</th>
        <th style="width: 20%;">Action</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= $row['id'] ?></td>
          <td class="text-start"><?= htmlspecialchars($row['name']) ?></td>
          <td>
            <form method="post" class="d-inline">
              <input type="hidden" name="id" value="<?= $row['id'] ?>">
              <button name="delete" class="btn btn-delete btn-sm">Delete</button>
            </form>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>

  <a href="admin_dashboard.php" class="btn btn-back">← Back to Dashboard</a>
</body>
</html>
