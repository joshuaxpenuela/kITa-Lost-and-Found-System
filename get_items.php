<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "lost_found_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$current_date = date('Y-m-d');
$one_week_ago = date('Y-m-d', strtotime('-7 days'));

$sql = "SELECT id_item, img1, item_name, location_found, report_date, report_time, status, other_details,
        CASE
            WHEN report_date = '$current_date' THEN 'today'
            WHEN report_date BETWEEN '$one_week_ago' AND '$current_date' THEN 'week'
            ELSE 'older'
        END AS category
        FROM reported_items
        WHERE (status = 'Unclaimed' OR status = 'Claimed') AND (remark = 'Approved' OR remark = 'Pending')
        ORDER BY report_date DESC, report_time DESC";

$result = $conn->query($sql);
$items = array();

if ($result->num_rows > 0) {
    $today_count = 0;
    $week_count = 0;
    $older_count = 0;
    while ($row = $result->fetch_assoc()) {
        $category = $row['category'];
        
        if ($category === 'today' && $today_count < 3) {
            $items[] = $row;
            $today_count++;
        } elseif ($category === 'week' && $week_count < 3) {
            $items[] = $row;
            $week_count++;
        } elseif ($category === 'older' && $older_count < 3) {
            $items[] = $row;
            $older_count++;
        }
        if ($today_count >= 3 && $week_count >= 3 && $older_count >= 3) {
            break;
        }
    }
}

$conn->close();
header('Content-Type: application/json');
echo json_encode($items);
?>
