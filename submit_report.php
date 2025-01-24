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

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log incoming data
error_log("POST Data: " . print_r($_POST, true));
error_log("FILES Data: " . print_r($_FILES, true));

// Get POST data with validation
$Fname = isset($_POST['Fname']) ? $conn->real_escape_string($_POST['Fname']) : '';
$Lname = isset($_POST['Lname']) ? $conn->real_escape_string($_POST['Lname']) : '';
$email = isset($_POST['email']) ? $conn->real_escape_string($_POST['email']) : '';
$contact_no = isset($_POST['contact_no']) ? $conn->real_escape_string($_POST['contact_no']) : '';
$dept_college = isset($_POST['dept_college']) ? $conn->real_escape_string($_POST['dept_college']) : '';
$item_name = isset($_POST['item_name']) ? $conn->real_escape_string($_POST['item_name']) : '';
$item_category = isset($_POST['item_category']) ? $conn->real_escape_string($_POST['item_category']) : '';
$location_found = isset($_POST['location_found']) ? $conn->real_escape_string($_POST['location_found']) : '';
$date = isset($_POST['report_date']) ? $conn->real_escape_string($_POST['report_date']) : '';
$time = isset($_POST['report_time']) ? $conn->real_escape_string($_POST['report_time']) : '';
$other_details = isset($_POST['other_details']) ? $conn->real_escape_string($_POST['other_details']) : '';

// Handle file uploads
$uploads_dir = 'uploads/img_reported_items/';
if (!file_exists($uploads_dir)) {
    mkdir($uploads_dir, 0777, true);
}

$images = array();
for ($i = 1; $i <= 5; $i++) {
    $fieldName = "img" . $i;
    $images[$fieldName] = '';
    
    if (isset($_FILES[$fieldName]) && $_FILES[$fieldName]['error'] == 0) {
        $temp = $_FILES[$fieldName]['tmp_name'];
        $name = uniqid() . '_' . basename($_FILES[$fieldName]['name']);
        
        if (move_uploaded_file($temp, $uploads_dir . $name)) {
            $images[$fieldName] = $name;
            error_log("Successfully uploaded file: " . $fieldName . " as " . $name);
        } else {
            error_log("Failed to move uploaded file: " . $fieldName . " - Error: " . error_get_last()['message']);
        }
    }
}

// Prepare and execute SQL with proper escaping
$sql = "INSERT INTO reported_items (
            Fname, Lname, email, contact_no, dept_college, 
            item_name, item_category, location_found, report_date, report_time, 
            other_details, img1, img2, img3, img4, img5, status, remark
        ) VALUES (
            ?, ?, ?, ?, ?,
            ?, ?, ?, ?, ?,
            ?, ?, ?, ?, ?, ?,
            'Unclaimed', 'Unapproved'
        )";

$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die(json_encode(['success' => false, 'message' => 'Prepare failed: ' . $conn->error]));
}

$stmt->bind_param("ssssssssssssssss",
    $Fname, $Lname, $email, $contact_no, $dept_college,
    $item_name, $item_category, $location_found, $date, $time,
    $other_details, $images['img1'], $images['img2'], $images['img3'], 
    $images['img4'], $images['img5']
);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Report submitted successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>