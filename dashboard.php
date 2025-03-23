<?php
include 'db.php';
session_start();

if ($_SESSION['role'] !== 'admin') {
    die("Access denied.");
}

echo "<h2>Feedback Overview</h2>";

$result = $conn->query("SELECT f.feedback, c.course_name, u.username FROM feedback f 
JOIN courses c ON f.course_id = c.id 
JOIN users u ON f.student_id = u.id");

echo "<div class='container'>";
while ($row = $result->fetch_assoc()) {
    echo "<p><strong>" . ($row['username'] ?? "Anonymous") . " on " . $row['course_name'] . ":</strong> " . $row['feedback'] . "</p>";
}
echo "</div>";
?>
