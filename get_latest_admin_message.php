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

$query = "SELECT DISTINCT sender_id, receiver_id, message, created_at 
          FROM admin_message 
          ORDER BY created_at DESC 
          LIMIT 1";

$result = $conn->query($query);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $response['success'] = true;
    $response['messages'] = array(
        array(
            "sender_id" => $row['sender_id'],
            "receiver_id" => $row['receiver_id'],
            "message" => $row['message'],
            "created_at" => $row['created_at']
        )
    );
} else {
    $response['success'] = false;
    $response['error'] = "No messages found.";
}

echo json_encode($response);

$conn->close();
?>