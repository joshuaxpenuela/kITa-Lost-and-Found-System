<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['admin']) || !isset($_GET['user_id'])) {
    exit('Unauthorized or missing data');
}

$admin_username = $_SESSION['admin'];
$user_id = $_GET['user_id'];

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

$sql = "SELECT * FROM admin_message 
        WHERE (sender_id = ? AND receiver_id = ?) 
           OR (admin_id = ? AND user_id = ?) 
        ORDER BY created_at ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iiii", $user_id, $admin_id, $admin_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$messages = $result->fetch_all(MYSQLI_ASSOC);

foreach ($messages as $message) {
    $is_admin_message = $message['admin_id'] == $admin_id;
    $message_class = $is_admin_message ? 'admin-message' : 'user-message';
    $align_class = $is_admin_message ? 'text-end' : 'text-start';
    
    echo "<div class='message-row $align_class'>";
    echo "<div class='message $message_class'>";
    echo htmlspecialchars($message['message']);
    echo "<br><small>" . date('M d, Y H:i', strtotime($message['created_at'])) . "</small>";
    echo "</div></div>";
}
?>