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

$response = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id'];

    // Fetch the conversation history for the user
    $sql = "SELECT sender_id, receiver_id, message, created_at FROM user_messages 
            WHERE sender_id = ? OR receiver_id = ? ORDER BY created_at ASC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $user_id);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $conversations = array();

        while ($row = $result->fetch_assoc()) {
            $conversation = array(
                "sender_id" => $row['sender_id'],
                "receiver_id" => $row['receiver_id'],
                "message" => $row['message'],
                "created_at" => $row['created_at']
            );
            $conversations[] = $conversation;
        }

        $response['success'] = true;
        $response['conversations'] = $conversations;
    } else {
        $response['success'] = false;
        $response['message'] = "Failed to load conversations.";
    }

    $stmt->close();
} else {
    $response['success'] = false;
    $response['message'] = "Invalid request.";
}

$conn->close();
echo json_encode($response);
?>