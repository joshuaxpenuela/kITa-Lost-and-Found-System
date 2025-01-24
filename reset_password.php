<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "lost_found_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$email = $_POST['email'];
$new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

$sql = "UPDATE users SET password = '$new_password', otp = NULL, reset_pass_time = NULL WHERE email = '$email'";

if ($conn->query($sql) === TRUE) {
    echo json_encode(['status' => 'success', 'message' => 'Password updated successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Error updating password: ' . $conn->error]);
}

$conn->close();
?>
