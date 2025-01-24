<?php
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
$otp = $_POST['otp'];

// Check if email and OTP match in the database
$sql = "SELECT * FROM users WHERE email = '$email' AND otp = '$otp'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // OTP is valid
    // You may want to add a timestamp check here to ensure the OTP hasn't expired
    echo json_encode(['status' => 'success', 'message' => 'OTP verified successfully']);
} else {
    // OTP is invalid
    echo json_encode(['status' => 'error', 'message' => 'Invalid OTP']);
}

$conn->close();
?>