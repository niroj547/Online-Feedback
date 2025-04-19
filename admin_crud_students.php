<?php
session_start();
include 'db_config.php';

if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.html");
    exit();
}

// Insert Student
if (isset($_POST['add'])) {
    $name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $student_id = $_POST['student_id'] ?? '';
    $year = $_POST['academic_year'] ?? '';
    $sem = $_POST['semester'] ?? '';
    $section = $_POST['section'] ?? '';
    $program = $_POST['program'] ?? '';
    
    if (!empty($name) && !empty($email) && !empty($password) && !empty($student_id)) {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM students WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 0) {
            $stmt = $conn->prepare("INSERT INTO students (full_name, email, password, student_id, academic_year, semester, section, program) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssss", $name, $email, $password, $student_id, $year, $sem, $section, $program);
            $stmt->execute();
        } else {
            echo "<script>alert('Email already exists.');</script>";
        }
    }
}

// Delete Student
if (isset($_POST['delete'])) {
    $id = intval($_POST['delete_id'] ?? 0);
    if ($id > 0) {
        $stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    }
}

$students = $conn->query("SELECT * FROM students ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
  <title>Manage Students</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4 bg-light">
  <div class="container">
    <h2 class="mb-4">Manage Students</h2>

    <form method="post" class="row g-3 mb-4">
      <div class="col-md-6">
        <input type="text" name="full_name" placeholder="Full Name" class="form-control" required>
      </div>
      <div class="col-md-6">
        <input type="email" name="email" placeholder="Email" class="form-control" required>
      </div>
      <div class="col-md-4">
        <input type="password" name="password" placeholder="Password" class="form-control" required>
      </div>
      <div class="col-md-4">
        <input type="text" name="student_id" placeholder="Student ID (e.g., L5CG22-30)" class="form-control" required>
      </div>
      <div class="col-md-4">
        <input type="text" name="academic_year" value="Year 2" class="form-control" required>
      </div>
      <div class="col-md-4">
        <input type="text" name="semester" value="4th" class="form-control" required>
      </div>
      <div class="col-md-4">
        <input type="text" name="section" value="L5CG22" class="form-control" required>
      </div>
      <div class="col-md-4">
        <input type="text" name="program" value="BSc(Hons)CS" class="form-control" required>
      </div>
      <div class="col-12">
        <button type="submit" name="add" class="btn btn-success">Add Student</button>
        <a href="admin_dashboard.php" class="btn btn-secondary float-end">← Back to Dashboard</a>
      </div>
    </form>

    <div class="table-responsive">
      <table class="table table-bordered table-hover">
        <thead class="table-dark text-center">
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Student ID</th>
            <th>Year</th>
            <th>Semester</th>
            <th>Section</th>
            <th>Program</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody class="text-center">
          <?php while ($row = $students->fetch_assoc()): ?>
            <tr>
              <td><?= $row['id'] ?></td>
              <td><?= htmlspecialchars($row['full_name']) ?></td>
              <td><?= htmlspecialchars($row['email']) ?></td>
              <td><?= $row['student_id'] ?></td>
              <td><?= $row['academic_year'] ?></td>
              <td><?= $row['semester'] ?></td>
              <td><?= $row['section'] ?></td>
              <td><?= $row['program'] ?></td>
              <td>
                <form method="post" onsubmit="return confirm('Are you sure to delete this student?');">
                  <input type="hidden" name="delete_id" value="<?= $row['id'] ?>">
                  <button name="delete" class="btn btn-danger btn-sm">Delete</button>
                </form>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>
