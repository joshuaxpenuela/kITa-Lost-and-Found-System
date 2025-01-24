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
    $receiver_id = $_POST['receiver_id'];
    $message = $_POST['message'];
    $created_at = date('Y-m-d H:i:s');

    $stmt = $conn->prepare("INSERT INTO user_message (sender_id, receiver_id, message, created_at) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $sender_id, $receiver_id, $message, $created_at);

    if ($stmt->execute()) {
        $response['success'] = true;
    } else {
        $response['success'] = false;
        $response['error'] = 'Failed to send message';
    }

    $stmt->close();
} else {
    $response['success'] = false;
    $response['error'] = 'Invalid request';
}

echo json_encode($response);
$conn->close();
?>
