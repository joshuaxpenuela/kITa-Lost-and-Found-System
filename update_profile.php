<?php
header("Content-Type: application/json; charset=UTF-8");
// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "lost_found_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    $response = array("status" => "error", "message" => "Connection failed: " . $conn->connect_error);
    echo json_encode($response);
    exit();
}

// Capture data from the Android app's POST request
$id = isset($_POST['id']) ? $_POST['id'] : null;
$firstName = isset($_POST['firstName']) ? $_POST['firstName'] : null;
$lastName = isset($_POST['lastName']) ? $_POST['lastName'] : null;
$email = isset($_POST['email']) ? $_POST['email'] : null;
$contactNo = isset($_POST['contactNo']) ? $_POST['contactNo'] : null;
$department = isset($_POST['department']) ? $_POST['department'] : null;

// Check if required parameters are present
if ($id === null || $firstName === null || $lastName === null || $email === null || $contactNo === null || $department === null) {
    $response = array("status" => "error", "message" => "Missing required parameters.");
    echo json_encode($response);
    exit();
}

// Check if contact number exists for other users
$checkContactSql = "SELECT email FROM users WHERE contactNo = ? AND email != ?";
$checkStmt = $conn->prepare($checkContactSql);
$checkStmt->bind_param("ss", $contactNo, $email);
$checkStmt->execute();
$result = $checkStmt->get_result();

if ($result->num_rows > 0) {
    $response = array("status" => "error", "message" => "Contact number already exists");
    echo json_encode($response);
    $checkStmt->close();
    $conn->close();
    exit();
}
$checkStmt->close();

// SQL query to update the user's profile based on the email
$sql = "UPDATE users SET Fname = ?, Lname = ?, contactNo = ?, dept = ? WHERE email = ?";

// Prepare and bind the statement
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    $response = array("status" => "error", "message" => "Prepare failed: " . $conn->error);
    echo json_encode($response);
    exit();
}

$stmt->bind_param("sssss", $firstName, $lastName, $contactNo, $department, $email);

// Execute the statement and check if it was successful
if ($stmt->execute()) {
    $response = array("status" => "success", "message" => "Profile updated successfully.");
} else {
    $response = array("status" => "error", "message" => "Failed to update profile: " . $stmt->error);
}

// Close the statement and connection
$stmt->close();
$conn->close();

// Return the response in JSON format
echo json_encode($response);
?>