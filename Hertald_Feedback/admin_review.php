<?php
include 'db_config.php';

// Approve feedback
if (isset($_GET['approve'])) {
    $id = intval($_GET['approve']);
    $conn->query("UPDATE feedback SET verified = 1 WHERE id = $id");
    header("Location: admin_review.php?success=verified");
    exit();
}

// Delete feedback
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM feedback WHERE id = $id");
    header("Location: admin_review.php?success=deleted");
    exit();
}

// Fetch feedback details if preview requested
$preview = null;
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $preview = $conn->query("SELECT f.*, 
        c.name AS course_name, 
        l.name AS lecturer_name, 
        t.name AS tutor_name 
        FROM feedback f
        LEFT JOIN course c ON f.course_id = c.id
        LEFT JOIN lecturer l ON f.lecturer_id = l.id
        LEFT JOIN tutor t ON f.tutor_id = t.id
        WHERE f.id = $id")->fetch_assoc();
}

// Fetch unverified feedback
$unverified = $conn->query("SELECT f.id, f.student_name, f.feedback_date, c.name AS course_name 
    FROM feedback f
    LEFT JOIN course c ON f.course_id = c.id
    WHERE f.verified = 0
    ORDER BY f.feedback_date DESC");
?>

<!DOCTYPE html>
<html>
<head>
  <title>Admin - Review Feedback</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: white;
      color: black;
    }

    .card {
      border-radius: 1rem;
      box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.05);
    }

    .table {
      border-radius: 0.5rem;
      overflow: hidden;
    }

    .btn-custom {
      background-color: white;
      border-width: 1.5px;
      transition: all 0.2s ease-in-out;
    }

    /* Button Colors */
    .btn-success.btn-custom {
      border-color: #198754;
      color: #198754;
    }

    .btn-success.btn-custom:hover {
      background-color: #198754;
      color: white;
    }

    .btn-danger.btn-custom {
      border-color: #dc3545;
      color: #dc3545;
    }

    .btn-danger.btn-custom:hover {
      background-color: #dc3545;
      color: white;
    }

    .btn-secondary.btn-custom {
      border-color: #6c757d;
      color: #6c757d;
    }

    .btn-secondary.btn-custom:hover {
      background-color: #6c757d;
      color: white;
    }

    .btn-primary.btn-custom {
      border-color: #0d6efd;
      color: #0d6efd;
    }

    .btn-primary.btn-custom:hover {
      background-color: #0d6efd;
      color: white;
    }

    .table td, .table th {
      vertical-align: middle;
    }

    .table thead {
      background-color: #f2f2f2;
    }

    .card-header {
      background-color: #e9ecef;
      font-weight: 600;
    }

    a {
      text-decoration: none;
    }
  </style>
</head>
<body>
<div class="container py-5">
  <h2 class="mb-4 fw-bold">Pending Feedback Review</h2>

  <?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success rounded-3">
      <?= $_GET['success'] === 'verified' ? 'Feedback verified successfully.' : 'Feedback deleted.' ?>
    </div>
  <?php endif; ?>

  <?php if ($preview): ?>
    <!-- Full Feedback Preview -->
    <div class="card mb-4">
      <div class="card-header">Full Feedback Report</div>
      <div class="card-body">
        <p><strong>Course:</strong> <?= htmlspecialchars($preview['course_name'] ?? 'N/A') ?></p>
        <p><strong>Student:</strong> <?= $preview['anonymous_mode'] ? 'Anonymous' : htmlspecialchars($preview['student_name'] ?? 'N/A') ?></p>
        <p><strong>Date:</strong> <?= htmlspecialchars($preview['feedback_date'] ?? 'N/A') ?></p>
        <hr>
        <p><strong>Lecturer:</strong> <?= htmlspecialchars($preview['lecturer_name'] ?? 'N/A') ?></p>
        <p><strong>Rating:</strong> <?= str_repeat('★', (int)($preview['lecturer_rating'] ?? 0)) ?></p>
        <p><strong>Feedback:</strong><br>
          <?= nl2br(htmlspecialchars(($preview['lecturer_comment'] !== null && $preview['lecturer_comment'] !== '') ? $preview['lecturer_comment'] : 'No comment')) ?>
        </p>
        <hr>
        <p><strong>Tutor:</strong> <?= htmlspecialchars($preview['tutor_name'] ?? 'N/A') ?></p>
        <p><strong>Rating:</strong> <?= str_repeat('★', (int)($preview['tutor_rating'] ?? 0)) ?></p>
        <p><strong>Feedback:</strong><br>
          <?= nl2br(htmlspecialchars(($preview['tutor_comment'] !== null && $preview['tutor_comment'] !== '') ? $preview['tutor_comment'] : 'No comment')) ?>
        </p>
      </div>
      <div class="card-footer d-flex flex-wrap justify-content-between gap-2">
        <a href="?approve=<?= $preview['id'] ?>" class="btn btn-success btn-custom" onclick="return confirm('Approve this feedback?')">Approve</a>
        <a href="?delete=<?= $preview['id'] ?>" class="btn btn-danger btn-custom" onclick="return confirm('Delete this feedback?')">Delete</a>
        <a href="admin_review.php" class="btn btn-secondary btn-custom">Back</a>
      </div>
    </div>

  <?php else: ?>
    <!-- Unverified Feedback Table -->
    <?php if ($unverified->num_rows > 0): ?>
      <div class="card">
        <div class="card-header">Unverified Feedback List</div>
        <div class="table-responsive">
          <table class="table table-bordered table-hover mb-0">
            <thead>
              <tr>
                <th>Date</th>
                <th>Course</th>
                <th>Student</th>
                <th class="text-center">Action</th>
              </tr>
            </thead>
            <tbody>
              <?php while ($row = $unverified->fetch_assoc()): ?>
                <tr>
                  <td><?= htmlspecialchars($row['feedback_date'] ?? '') ?></td>
                  <td><?= htmlspecialchars($row['course_name'] ?? '') ?></td>
                  <td><?= $row['student_name'] ? htmlspecialchars($row['student_name']) : '<i>Anonymous</i>' ?></td>
                  <td class="text-center">
                    <a href="?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary btn-custom me-1">View</a>
                    <a href="?delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger btn-custom" onclick="return confirm('Delete this feedback?')">Delete</a>
                  </td>
                </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      </div>
    <?php else: ?>
      <div class="alert alert-info rounded-3">No unverified feedback at the moment.</div>
    <?php endif; ?>
  <?php endif; ?>
</div>
</body>
</html>
