<?php
session_start();
require_once __DIR__ . '/../db_config.php';

if (!isset($_SESSION['student'])) {
    header("Location: student_login.html");
    exit();
}
