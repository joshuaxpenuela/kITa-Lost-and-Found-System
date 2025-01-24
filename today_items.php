<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "lost_found_db";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Modify the SQL query to include both date and time, and order by both
$sql = "SELECT img1, item_name, location_found, report_date, time FROM reported_items ORDER BY date DESC, time DESC LIMIT 3";
$result = $conn->query($sql);
$items = array();
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $items[] = $row;
    }
}
$conn->close();
header('Content-Type: application/json');
echo json_encode($items);
?>