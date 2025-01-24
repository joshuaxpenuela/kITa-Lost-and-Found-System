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

function getLatestAdminMessage($userId) {
    global $conn;
    $sql = "SELECT * FROM admin_message WHERE receiver_id = ? ORDER BY created_at DESC LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function getMessages($userId) {
    global $conn;
    $sql = "SELECT * FROM admin_message WHERE sender_id = ? OR receiver_id = ? ORDER BY created_at ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $userId, $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

function sendMessage($senderId, $message, $mediaUrl = null) {
    global $conn;
    
    // Get all admin IDs
    $adminIds = getAllAdminIds();
    
    $success = true;
    
    foreach ($adminIds as $adminId) {
        $sql = "INSERT INTO admin_message (sender_id, receiver_id, message, media_url) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiss", $senderId, $adminId, $message, $mediaUrl);
        if (!$stmt->execute()) {
            $success = false;
            break;
        }
    }
    
    return $success;
}

function getAllAdminIds() {
    global $conn;
    $sql = "SELECT id FROM admins";
    $result = $conn->query($sql);
    $adminIds = [];
    while ($row = $result->fetch_assoc()) {
        $adminIds[] = $row['id'];
    }
    return $adminIds;
}
?>