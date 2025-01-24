<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "lost_found_db";
// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$email = $_POST['email'];
// Check if email exists in the database
$sql = "SELECT * FROM users WHERE email = '$email'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $otp = rand(100000, 999999);
    $reset_pass_time = date("Y-m-d H:i:s");
    // Update OTP and reset_pass_time in the database
    $sql = "UPDATE users SET otp = '$otp', reset_pass_time = '$reset_pass_time' WHERE email = '$email'";
    $conn->query($sql);
    // Send OTP via email using PHPMailer
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'kitak2450@gmail.com';
        $mail->Password = 'nrhhlphbnfpswsve';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;
        //Recipients
        $mail->setFrom('kitak2450@gmail.com', 'kITa Lost & Found System');
        $mail->addAddress($email);
        // Content
        $mail->isHTML(true);
        $mail->Subject = 'OTP Code for Reset Password';
        $mail->Body    = 'Your OTP code is ' . $otp;
        $mail->send();
        echo json_encode(['status' => 'success', 'message' => 'OTP sent']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error sending OTP']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Email not found']);
}
$conn->close();
?>