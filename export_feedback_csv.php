<?php
include 'db_config.php';

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=feedback_export.csv');

$output = fopen("php://output", "w");

// Column headings
fputcsv($output, ['Student Name', 'Course', 'Lecturer Rating', 'Tutor Rating', 'Comment', 'Anonymous']);

$query = "
    SELECT f.student_name, c.name AS course, f.lecturer_rating, f.tutor_rating, f.comment, f.anonymous_mode 
    FROM feedback f 
    JOIN course c ON f.course_id = c.id
";

$result = $conn->query($query);

while ($row = $result->fetch_assoc()) {
    fputcsv($output, [
        $row['student_name'] ?: 'Anonymous',
        $row['course'],
        $row['lecturer_rating'],
        $row['tutor_rating'],
        $row['comment'],
        $row['anonymous_mode'] ? 'Yes' : 'No'
    ]);
}

fclose($output);
exit;
?>
