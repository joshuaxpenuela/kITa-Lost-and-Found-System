<?php
session_start();
@include 'db.php';

if (!isset($_SESSION['admin'])) {
    header('HTTP/1.1 401 Unauthorized');
    exit;
}

// Check for unviewed messages
$messages_query = "SELECT COUNT(*) as count FROM admin_message 
                  WHERE receiver_id = ? 
                  AND view_status = 0";
$stmt = $conn->prepare($messages_query);
$stmt->bind_param("i", $_SESSION['admin']);
$stmt->execute();
$messages_result = $stmt->get_result();
$has_new_messages = $messages_result->fetch_assoc()['count'] > 0;
$stmt->close();

// Check for unviewed unclaimed items
$unclaimed_query = "SELECT COUNT(*) as count FROM reported_items 
                   WHERE status = 'Unclaimed' 
                   AND remark = 'Approved' 
                   AND view_status = 0";
$stmt = $conn->prepare($unclaimed_query);
$stmt->execute();
$unclaimed_result = $stmt->get_result();
$has_new_unclaimed = $unclaimed_result->fetch_assoc()['count'] > 0;
$stmt->close();

// Prepare response (only messages and unclaimed)
$response = [
    'messages' => $has_new_messages,
    'unclaimed' => $has_new_unclaimed
];

header('Content-Type: application/json');
echo json_encode($response);
$conn->close();
?>