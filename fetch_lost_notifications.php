<?php
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');
// Define database credentials correctly
$host = "localhost";
$username = "root";  // default XAMPP username
$password = "";      // default XAMPP password
$database = "lost_found_db";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die(json_encode(array("error" => "Connection failed: " . $conn->connect_error)));
}

$email = isset($_POST['email']) ? $_POST['email'] : '';

if (empty($email)) {
    die(json_encode(array("error" => "Email is required")));
}

// Fetch found item notifications
$sql = "SELECT id_item, item_name, status, DATE(report_date) as notification_date 
        FROM reported_lost_items 
        WHERE email = ? AND status = 'Found' 
        ORDER BY report_date DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

$notifications = array();

while ($row = $result->fetch_assoc()) {
    $notifications[] = array(
        "item_name" => $row['item_name'],
        "notification_date" => $row['notification_date']
    );
}

echo json_encode($notifications);

$stmt->close();
$conn->close();
?>