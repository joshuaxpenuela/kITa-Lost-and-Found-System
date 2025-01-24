<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "lost_found_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

// Check if ID is provided
if (!isset($_GET['id_item'])) {
    echo json_encode(["error" => "No item ID provided"]);
    exit;
}

$itemId = $_GET['id_item'];

// Modified query to check user privacy settings
$query = "SELECT r.id_item, r.Fname, r.Lname, r.email, r.item_name, r.status, 
          r.location_found, r.report_date, r.report_time, r.item_category, 
          r.other_details, r.img1, r.img2, r.img3, r.img4, r.img5,
          u.dataPrivacy 
          FROM reported_items r 
          LEFT JOIN users u ON r.email = u.email 
          WHERE r.id_item = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $itemId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    
    // Handle privacy settings
    if ($row['dataPrivacy'] === 'Disagreed' || $row['dataPrivacy'] === NULL) {
        // If user disagreed or doesn't exist in users table, set name to Anonymous
        $row['Fname'] = 'Anonymous';
        $row['Lname'] = '';
    }
    
    // Remove dataPrivacy from response
    unset($row['dataPrivacy']);
    
    header('Content-Type: application/json');
    echo json_encode($row);
} else {
    echo json_encode(["error" => "No item found with ID: " . $itemId]);
}

$stmt->close();
$conn->close();
?>