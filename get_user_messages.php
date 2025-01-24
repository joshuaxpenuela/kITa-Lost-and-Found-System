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
    $user_id = $_POST['user_id'];

    // Fetch messages sent by admins to the user and messages sent by the user to admins
    $query = "SELECT DISTINCT sender_id, message, created_at 
              FROM admin_message 
              WHERE user_id = ? OR sender_id = ?
              ORDER BY created_at ASC";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $user_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $messages = array();
    while ($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }

    $response['success'] = true;
    $response['messages'] = $messages;
} else {
    $response['success'] = false;
    $response['error'] = "Invalid request method.";
}

echo json_encode($response);

$conn->close();
?>