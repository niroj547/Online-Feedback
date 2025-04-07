<?php
include 'db_config.php';

$courses = $conn->query("SELECT id, name FROM course");
$coursesArray = [];

while ($course = $courses->fetch_assoc()) {
    $coursesArray[] = [
        'id' => $course['id'],
        'name' => $course['name']
    ];
}

header('Content-Type: application/json');
echo json_encode($coursesArray);
?>