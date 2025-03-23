<?php
include 'db_config.php';

$email = $_POST['email'];
$password = $_POST['password'];

$sql = "SELECT * FROM admin WHERE email=? AND password=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $email, $password);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    header("Location: admin_dashboard.html");
    exit();
} else {
    echo "<script>alert('Invalid email or password'); window.history.back();</script>";
}
?>
