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
    $receiver_id = $_POST['receiver_id'];

    $stmt = $conn->prepare("SELECT * FROM user_message WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?) ORDER BY created_at ASC");
    $stmt->bind_param("iiii", $user_id, $receiver_id, $receiver_id, $user_id);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $messages = array();

        while ($row = $result->fetch_assoc()) {
            $messages[] = $row;
        }

        $response['success'] = true;
        $response['messages'] = $messages;
    } else {
        $response['success'] = false;
        $response['error'] = 'Failed to load messages';
    }

    $stmt->close();
} else {
    $response['success'] = false;
    $response['error'] = 'Invalid request';
}

echo json_encode($response);
$conn->close();
?>
