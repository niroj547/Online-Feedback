<?php
include 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add'])) {
        $name = $_POST['name'];
        $conn->query("INSERT INTO course (name) VALUES ('$name')");
    } elseif (isset($_POST['delete'])) {
        $id = $_POST['id'];
        $conn->query("DELETE FROM course WHERE id=$id");
    }
}

$courses = $conn->query("SELECT * FROM course");
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
