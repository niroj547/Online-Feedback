<?php
session_start();
require_once __DIR__ . '/../db_config.php';

if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.html");
    exit();
}
