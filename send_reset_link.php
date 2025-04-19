<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
require 'db_config.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'];

    // Check if user exists
    $stmt = $conn->prepare("SELECT id FROM students WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Generate reset token
        $token = bin2hex(random_bytes(32));
        $expires_at = date("Y-m-d H:i:s", strtotime("+30 minutes"));

        // Store token
        $stmt = $conn->prepare("UPDATE students SET reset_token = ?, reset_token_expires = ? WHERE email = ?");
        $stmt->bind_param("ssi", $token, $expires_at, $email);
        $stmt->execute();

        // Build reset link
        $resetLink = "http://localhost/reset_password.php?token=$token";

        // Send email
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'sandbox.smtp.mailtrap.io';
            $mail->SMTPAuth = true;
            $mail->Port = 587;
            $mail->Username = '0d1840b63b1dad';
            $mail->Password = 'f5f2599b8d025f';

            $mail->setFrom('noreply@herald.com', 'Herald Student Portal');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Reset Your Password';
            $mail->Body = "Click <a href='$resetLink'>here</a> to reset your password. This link expires in 30 minutes.";

            $mail->send();
            echo "Reset link sent to your email.";
        } catch (Exception $e) {
            echo "Error sending email: {$mail->ErrorInfo}";
        }
    } else {
        echo "Email not found!";
    }
}
?>