<?php
// search_items.php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "lost_found_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get search and category parameters
$search = isset($_GET['search']) ? $_GET['search'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';

// Updated SQL query to include img1
$sql = "SELECT id_item, item_name, location_found, img1, other_details 
        FROM reported_items 
        WHERE item_name LIKE ? 
          AND status = 'Unclaimed' 
          AND (remark = 'Approved' OR remark = 'Pending')";

// Array to hold parameters and their types
$params = [];
$types = "s"; // 's' for the search string
$params[] = "%$search%";

// Add category filter if provided
if (!empty($category)) {
    $sql .= " AND item_category = ?";
    $types .= "s"; // Add another string type for the category
    $params[] = $category;
}

// Append ORDER BY clause
$sql .= " ORDER BY report_date DESC, report_time DESC";

// Prepare and bind parameters
$stmt = $conn->prepare($sql);

// Bind the parameters dynamically using call_user_func_array
$stmt->bind_param($types, ...$params);

// Execute and fetch the results
$stmt->execute();
$result = $stmt->get_result();

$items = [];
while ($row = $result->fetch_assoc()) {
    $items[] = $row;
}

// Send the result as JSON
header('Content-Type: application/json');
echo json_encode($items);

$stmt->close();
$conn->close();
?>
