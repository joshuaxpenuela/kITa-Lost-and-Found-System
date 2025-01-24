<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "lost_found_db";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL query to get the latest claimed and approved items
$sql = "SELECT id_item, item_name, location_found, img1, other_details FROM reported_items 
        WHERE status = 'Claimed' AND remark = 'Approved' 
        ORDER BY report_date DESC, report_time DESC";

$result = $conn->query($sql);

$items = array();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $items[] = $row;
    }
}

// Return the results as JSON
header('Content-Type: application/json');
echo json_encode($items);

$conn->close();
?>
