<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
require 'db_config.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'];

    $stmt = $conn->prepare("SELECT id FROM students WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $token = bin2hex(random_bytes(32));
        $expires_at = date("Y-m-d H:i:s", strtotime("+30 minutes"));

        $stmt = $conn->prepare("UPDATE students SET reset_token = ?, reset_token_expires = ? WHERE email = ?");
        $stmt->bind_param("sss", $token, $expires_at, $email); // changed to "sss"
        $stmt->execute();

        $resetLink = "Ehttp://localhost/reset_password.php?token=$token";

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'sandbox.smtp.mailtrap.io';
            $mail->SMTPAuth = true;
            $mail->Port = 587; // try 2525 if 587 doesn't work
            $mail->Username = '0d1840b63b1dad';
            $mail->Password = 'f5f2599b8d025f';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // added
            $mail->setFrom('prasansahamal13@gmail.com', 'Herald Student Portal');
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
