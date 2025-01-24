<?php
// Disable warnings from appearing in output
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');

// Define database credentials correctly
$host = "localhost";
$username = "root";  // default XAMPP username
$password = "";      // default XAMPP password
$database = "lost_found_db";

try {
    $conn = new mysqli($host, $username, $password, $database);

    if ($conn->connect_error) {
        throw new Exception('Connection failed: ' . $conn->connect_error);
    }

    // Get user email from POST request
    $userEmail = isset($_POST['email']) ? $_POST['email'] : '';

    if (empty($userEmail)) {
        throw new Exception('Email is required');
    }

    // Prepare SQL query
    $query = "SELECT item_name, claim_date, claim_time 
              FROM claim_reports
              WHERE claim_email = ? 
              AND status = 'Claimed' 
              AND remark = 'Approved' 
              ORDER BY claim_time DESC, claim_date DESC";

    $stmt = $conn->prepare($query);
    
    if (!$stmt) {
        throw new Exception('Prepare failed: ' . $conn->error);
    }

    $stmt->bind_param("s", $userEmail);

    if (!$stmt->execute()) {
        throw new Exception('Execute failed: ' . $stmt->error);
    }

    $result = $stmt->get_result();
    
    $notifications = array();
    while ($row = $result->fetch_assoc()) {
        $notifications[] = array(
            'item_name' => $row['item_name'],
            'claim_date' => date('Y-m-d', strtotime($row['claim_date'])),
            'claim_time' => date('H:i:s', strtotime($row['claim_time']))
        );
    }

    echo json_encode($notifications);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
    if (isset($conn)) {
        $conn->close();
    }
}
?>