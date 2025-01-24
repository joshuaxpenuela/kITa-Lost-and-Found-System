<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['admin']) || !isset($_POST['user_id']) || !isset($_POST['message'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized or missing data']);
    exit;
}

$admin_username = $_SESSION['admin'];
$user_id = $_POST['user_id'];
$message = trim($_POST['message']);

// Fetch admin details
$stmt = $conn->prepare("SELECT id FROM admins WHERE username = ?");
$stmt->bind_param("s", $admin_username);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();

if (!$admin) {
    echo json_encode(['status' => 'error', 'message' => 'Admin not found']);
    exit;
}

$admin_id = $admin['id'];

// Validate message
if (empty($message)) {
    echo json_encode(['status' => 'error', 'message' => 'Message cannot be empty']);
    exit;
}

// Insert message with full details
$sql = "INSERT INTO admin_message (admin_id, user_id, message, sender_id, receiver_id) VALUES (?, ?, ?, 0, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iisi", $admin_id, $user_id, $message, $user_id);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Message sent successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Error sending message: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>