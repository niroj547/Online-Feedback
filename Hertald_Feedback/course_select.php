<?php
include 'db_config.php';

// Fetch course list
$courses_query = $conn->query("SELECT id, name FROM course");

if (!$courses_query) {
    die("<div style='color:red;'>Database Error: " . $conn->error . "</div>");
}

$courses = $courses_query->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Select Course</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: Arial, sans-serif;
      height: 100vh;
      background: linear-gradient(135deg, #c2e9fb, #fce2de);
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .card {
      background: white;
      padding: 3rem 4rem;
      border-radius: 30px;
      box-shadow: 0 6px 25px rgba(0, 0, 0, 0.1);
      text-align: center;
      width: 500px;
    }

    .card h2 {
      margin-bottom: 0.75rem;
      font-size: 2rem;
    }

    .card p {
      color: #666;
      font-size: 1rem;
      margin-bottom: 2rem;
    }

    .card button {
      display: block;
      width: 100%;
      margin: 0.75rem 0;
      padding: 1rem;
      border: 2px solid #00796b;
      border-radius: 25px;
      font-size: 1rem;
      font-weight: bold;
      background-color: white;
      color: #00796b;
      cursor: pointer;
      transition: all 0.2s ease-in-out;
    }

    .card button:hover {
      background-color: #00796b;
      color: white;
    }
  </style>
</head>
<body>

  <div class="card">
    <h2>Select Course</h2>
    <p>Choose a course to provide feedback and help us improve!</p>
    <button>Collaborative Development</button>
    <button>Human-Computer Interaction</button>
    <button>Distributed and Cloud Systems Programming</button>
  </div>

</body>
</html>
