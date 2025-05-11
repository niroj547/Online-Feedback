<?php
include 'db_config.php';

// Fetch data for analytics
$feedback_data = $conn->query("
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
")->fetch_all(MYSQLI_ASSOC);

// Prepare data for Chart.js
$course_names = [];
$lecturer_ratings = [];
$tutor_ratings = [];
$total_feedbacks = [];

foreach ($feedback_data as $data) {
    $course_names[] = $data['course_name'];
    $lecturer_ratings[] = round($data['avg_lecturer_rating'], 2);
    $tutor_ratings[] = round($data['avg_tutor_rating'], 2);
    $total_feedbacks[] = $data['total_feedbacks'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Feedback Analytics</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-light">
<div class="container py-4">
  <h2 class="mb-4">Feedback Analytics</h2>
  <a href="admin_dashboard.php" class="btn btn-secondary mb-4">← Back to Dashboard</a>

  <div class="mb-5">
    <h4>Average Ratings per Course</h4>
    <canvas id="ratingsChart"></canvas>
  </div>

  <div>
    <h4>Total Feedbacks per Course</h4>
    <canvas id="feedbacksChart"></canvas>
  </div>
</div>

<script>
  // Data for Average Ratings Chart
  const ratingsChartCtx = document.getElementById('ratingsChart').getContext('2d');
  new Chart(ratingsChartCtx, {
    type: 'bar',
    data: {
      labels: <?= json_encode($course_names) ?>,
      datasets: [
        {
          label: 'Lecturer Rating',
          data: <?= json_encode($lecturer_ratings) ?>,
          backgroundColor: 'rgba(75, 192, 192, 0.6)',
          borderColor: 'rgba(75, 192, 192, 1)',
          borderWidth: 1
        },
        {
          label: 'Tutor Rating',
          data: <?= json_encode($tutor_ratings) ?>,
          backgroundColor: 'rgba(153, 102, 255, 0.6)',
          borderColor: 'rgba(153, 102, 255, 1)',
          borderWidth: 1
        }
      ]
    },
    options: {
      responsive: true,
      plugins: {
        legend: {
          position: 'top'
        },
        title: {
          display: true,
          text: 'Average Ratings per Course'
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          max: 5
        }
      }
    }
  });

  // Data for Total Feedbacks Chart
  const feedbacksChartCtx = document.getElementById('feedbacksChart').getContext('2d');
  new Chart(feedbacksChartCtx, {
    type: 'pie',
    data: {
      labels: <?= json_encode($course_names) ?>,
      datasets: [{
        label: 'Total Feedbacks',
        data: <?= json_encode($total_feedbacks) ?>,
        backgroundColor: [
          'rgba(255, 99, 132, 0.6)',
          'rgba(54, 162, 235, 0.6)',
          'rgba(255, 206, 86, 0.6)',
          'rgba(75, 192, 192, 0.6)',
          'rgba(153, 102, 255, 0.6)',
          'rgba(255, 159, 64, 0.6)'
        ],
        borderColor: [
          'rgba(255, 99, 132, 1)',
          'rgba(54, 162, 235, 1)',
          'rgba(255, 206, 86, 1)',
          'rgba(75, 192, 192, 1)',
          'rgba(153, 102, 255, 1)',
          'rgba(255, 159, 64, 1)'
        ],
        borderWidth: 1
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: {
          position: 'top'
        },
        title: {
          display: true,
          text: 'Total Feedbacks per Course'
        }
      }
    }
  });
</script>
</body>
</html>
