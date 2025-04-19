<?php
require 'db_config.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $token = $_POST['token'];
    $newPassword = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

    // Check token validity
    $stmt = $conn->prepare("SELECT id, reset_token_expires FROM students WHERE reset_token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $current_time = date("Y-m-d H:i:s");
        if ($row['reset_token_expires'] > $current_time) {
            // Update password
            $stmt = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_token_expires = NULL WHERE id = ?");
            $stmt->bind_param("si", $newPassword, $row['id']);
            $stmt->execute();

            echo "Password updated successfully!";
        } else {
            echo "Token expired. Request a new password reset.";
        }
    } else {
        echo "Invalid or used token.";
    }
}
?>a<?php
require 'db_config.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $token = $_POST['token'];
    $newPassword = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

    // Check token validity
    $stmt = $conn->prepare("SELECT id, reset_token_expires FROM students WHERE reset_token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $current_time = date("Y-m-d H:i:s");
        if ($row['reset_token_expires'] > $current_time) {
            // Update password
            $stmt = $conn->prepare("UPDATE students SET password = ?, reset_token = NULL, reset_token_expires = NULL WHERE id = ?");
            $stmt->bind_param("si", $newPassword, $row['id']);
            $stmt->execute();

            echo "Password updated successfully!";
        } else {
            echo "Token expired. Request a new password reset.";
        }
    } else {
        echo "Invalid or used token.";
    }
}
?>