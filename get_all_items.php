<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "lost_found_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT id_item, item_name, location_found, img1, report_date, report_time, status, other_details,
        CASE
            WHEN report_date = CURDATE() THEN 'today'
            WHEN report_date >= CURDATE() - INTERVAL 7 DAY THEN 'week'
            ELSE 'older'
        END AS category
        FROM reported_items
        WHERE status = 'Unclaimed' AND (remark = 'Approved' OR remark = 'Pending')
        ORDER BY report_date DESC, report_time DESC";

$result = $conn->query($sql);
$items = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
    }
}

$conn->close();
header('Content-Type: application/json');
echo json_encode($items);
?>
