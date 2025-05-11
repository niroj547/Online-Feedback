<?php
include 'db_config.php';

// Fetch analytics data
$query = "
    SELECT 
        c.name AS course_name,
        AVG(f.lecturer_rating) AS avg_lecturer_rating,
        AVG(f.tutor_rating) AS avg_tutor_rating,
        COUNT(f.id) AS total_feedbacks
    FROM feedback f
    LEFT JOIN course c ON f.course_id = c.id
    WHERE f.verified = 1
    GROUP BY c.name
    ORDER BY c.name ASC
";
$result = $conn->query($query);

if (!$result) {
    die("Error fetching analytics data: " . $conn->error);
}

// Set headers for CSV download
header('Content-Type: text/csv');
header('Content-Disposition: attachment;filename="feedback_analytics.csv"');

// Open output stream
$output = fopen('php://output', 'w');

// Write CSV headers
fputcsv($output, ['Course Name', 'Average Lecturer Rating', 'Average Tutor Rating', 'Total Feedbacks']);

// Write data rows
while ($row = $result->fetch_assoc()) {
    fputcsv($output, [
        $row['course_name'],
        round($row['avg_lecturer_rating'], 2),
        round($row['avg_tutor_rating'], 2),
        $row['total_feedbacks']
    ]);
}

// Close output stream
fclose($output);
exit();
