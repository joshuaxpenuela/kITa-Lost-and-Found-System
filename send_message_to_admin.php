<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "lost_found_db";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$response = array();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sender_id = $_POST['sender_id'];
    $message = $_POST['message'];
    $created_at = $_POST['created_at'] ?? date('Y-m-d H:i:s');
    
    // Prepare the insert statement for the admin_message table
    $stmt = $conn->prepare("INSERT INTO admin_message (sender_id, receiver_id, user_id, message, created_at) VALUES (?, ?, ?, ?, ?)");
    
    // Fetch all admin IDs from the admins table
    $admin_ids = array();
    $admin_query = "SELECT id FROM admins";
    $result = $conn->query($admin_query);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $admin_ids[] = $row['id'];
        }
    }
    
    $success = true;
    foreach ($admin_ids as $admin_id) {
        $stmt->bind_param("iiiss", $sender_id, $admin_id, $sender_id, $message, $created_at);
        if (!$stmt->execute()) {
            $success = false;
            break;
        }
    }
    
    $response['success'] = $success;
} else {
    $response['success'] = false;
}

echo json_encode($response);

$stmt->close();
$conn->close();
?>