<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "lost_found_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get POST data
$fname = $_POST['Fname'];
$lname = $_POST['Lname'];
$email = $_POST['email'];
$password = $_POST['password'];
$contactNo = $_POST['contactNo'];
$dept = $_POST['dept'];
$dataPrivacy = $_POST['dataPrivacy'];

// First check if email exists
$stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    echo "email_exists";
    $stmt->close();
    $conn->close();
    exit();
}

// Then check if contact number exists
$stmt = $conn->prepare("SELECT * FROM users WHERE contactNo = ?");
$stmt->bind_param("s", $contactNo);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    echo "contact_exists";
    $stmt->close();
    $conn->close();
    exit();
}

// If neither exists, proceed with registration
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
$stmt = $conn->prepare("INSERT INTO users (Fname, Lname, email, password, contactNo, dept, dataPrivacy) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssss", $fname, $lname, $email, $hashedPassword, $contactNo, $dept, $dataPrivacy);

if ($stmt->execute()) {
    echo "success";
} else {
    echo "error: " . $stmt->error;
}

// Close connection
$stmt->close();
$conn->close();
?>