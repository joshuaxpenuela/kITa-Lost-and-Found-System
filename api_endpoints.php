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

header("Content-Type: application/json");

$action = isset($_GET['action']) ? $_GET['action'] : '';

switch ($action) {
    case 'getLatestAdminMessage':
        $userId = isset($_GET['userId']) ? intval($_GET['userId']) : 0;
        $message = getLatestAdminMessage($userId);
        echo json_encode($message);
        break;

    case 'getMessages':
        $userId = isset($_GET['userId']) ? intval($_GET['userId']) : 0;
        $messages = getMessages($userId);
        echo json_encode($messages);
        break;

    case 'sendMessage':
        $senderId = isset($_POST['senderId']) ? intval($_POST['senderId']) : 0;
        $message = isset($_POST['message']) ? $_POST['message'] : '';
        $mediaUrl = isset($_POST['mediaUrl']) ? $_POST['mediaUrl'] : null;

        if ($senderId && ($message || $mediaUrl)) {
            $result = sendMessage($senderId, $message, $mediaUrl);
            echo json_encode(['success' => $result]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Invalid parameters']);
        }
        break;

    default:
        echo json_encode(['error' => 'Invalid action']);
        break;
}
?>