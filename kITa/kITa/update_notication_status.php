<?php
session_start();
@include 'db.php';

if (!isset($_SESSION['admin'])) {
    header('HTTP/1.1 401 Unauthorized');
    exit;
}

$type = $_POST['type'] ?? '';
$response = ['success' => false, 'message' => 'Invalid request'];

switch ($type) {
    case 'messages':
        $query = "UPDATE admin_message 
                 SET view_status = 1 
                 WHERE receiver_id = ? 
                 AND view_status = 0";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $_SESSION['admin']);
        $success = $stmt->execute();
        $stmt->close();
        break;

    case 'unclaimed':
        $query = "UPDATE reported_items 
                 SET view_status = 1 
                 WHERE status = 'Unclaimed' 
                 AND remark = 'Approved' 
                 AND view_status = 0";
        $stmt = $conn->prepare($query);
        $success = $stmt->execute();
        $stmt->close();
        break;

    default:
        $success = false;
}

if ($success) {
    $response = ['success' => true, 'message' => 'Notifications updated successfully'];
}

header('Content-Type: application/json');
echo json_encode($response);
$conn->close();
?>