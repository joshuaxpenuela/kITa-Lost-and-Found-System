<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['admin']) || !isset($_POST['user_id']) || !isset($_POST['message'])) {
    exit('Unauthorized or missing data');
}

$admin_username = $_SESSION['admin'];
$user_id = $_POST['user_id'];
$message = $_POST['message'];

// Fetch admin details
$stmt = $conn->prepare("SELECT * FROM admins WHERE username = ?");
$stmt->bind_param("s", $admin_username);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();

if (!$admin) {
    exit('Admin not found');
}

$admin_id = $admin['id'];

$sql = "INSERT INTO admin_message (admin_id, user_id, message) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iis", $admin_id, $user_id, $message);

if ($stmt->execute()) {
    echo 'Message sent successfully';
} else {
    echo 'Error sending message: ' . $stmt->error;
}

$stmt->close();
$conn->close();
?>