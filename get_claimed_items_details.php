<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "lost_found_db";
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get item id from GET parameter
if (!isset($_GET['id_item'])) {
    echo json_encode(["error" => "No item ID provided"]);
    exit;
}

$id = $_GET['id_item'];

// SQL query to get the details of a specific claimed item
$sql = "SELECT * FROM reported_items WHERE id_item = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $item = $result->fetch_assoc();
    // Return the results as JSON
    header('Content-Type: application/json');
    echo json_encode($item);
} else {
    echo json_encode(array("error" => "Item not found"));
}

$stmt->close();
$conn->close();
?>
