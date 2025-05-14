<?php
session_start();
include 'db_config.php';

if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.html");
    exit();
}

// Add Course
if (isset($_POST['add'])) {
    $name = trim($_POST['name'] ?? '');
    if (!empty($name)) {
        $stmt = $conn->prepare("INSERT INTO course (name) VALUES (?)");
        $stmt->bind_param("s", $name);
        $stmt->execute();
        $stmt->close();
    }
}

// Delete Course
if (isset($_POST['delete'])) {
    $id = intval($_POST['id'] ?? 0);
    if ($id > 0) {
        // Check if the course exists
        $stmt = $conn->prepare("SELECT id FROM course WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $stmt = $conn->prepare("DELETE FROM course WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();
            
            // Reset auto-increment
            $conn->query("ALTER TABLE course AUTO_INCREMENT = 1");
        }
    }
}

$result = $conn->query("SELECT * FROM course");
?>

<h2>Manage Courses</h2>
<form method="post">
  <input type="text" name="name" placeholder="Course name" required>
  <button name="add">Add</button>
</form>

<table border="1">
  <tr><th>ID</th><th>Name</th><th>Action</th></tr>
  <?php while ($c = $courses->fetch_assoc()) { ?>
  <tr>
    <td><?= $c['id'] ?></td>
    <td><?= $c['name'] ?></td>
    <td>
      <form method="post" style="display:inline">
        <input type="hidden" name="id" value="<?= $c['id'] ?>">
        <button name="delete">Delete</button>
      </form>
    </td>
  </tr>
  <?php } ?>
</table>
<a href="admin_dashboard.php">← Back</a>
