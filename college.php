<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "lost_found_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => "Connection failed: " . $conn->connect_error]));
}

$sql = "SELECT college FROM colleges WHERE status = 'enabled' ORDER BY college ASC";
$result = $conn->query($sql);

$colleges = array();
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $colleges[] = $row['college'];
    }
    echo json_encode(['success' => true, 'colleges' => $colleges]);
} else {
    echo json_encode(['success' => false, 'message' => 'No colleges found']);
}

$conn->close();
?>