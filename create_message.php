<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "lost_found_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(["success" => false, "error" => "Connection failed: " . $conn->connect_error]);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);
if (!$data) {
    echo json_encode(["success" => false, "error" => "Invalid JSON input"]);
    exit();
}

$sender_id = $data['sender_id'] ?? null;
$receiver_id = $data['receiver_id'] ?? null;
$message = $data['message'] ?? null;
$media_url = $data['media_url'] ?? null;

if ($sender_id === null || $receiver_id === null || $message === null) {
    echo json_encode(["success" => false, "error" => "Missing required fields"]);
    exit();
}

$sql = "INSERT INTO admin_message (sender_id, receiver_id, message, media_url) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iiss", $sender_id, $receiver_id, $message, $media_url);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message_id" => $conn->insert_id]);
} else {
    echo json_encode(["success" => false, "error" => $stmt->error]);
}

$stmt->close();
$conn->close();
?>