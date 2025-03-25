<?php
session_start();
if (!isset($_SESSION['student'])) {
    header("Location: student_login.html");
    exit();
}

// Redirect to the feedback form
header("Location: feedback_form.html");
exit();
?>
