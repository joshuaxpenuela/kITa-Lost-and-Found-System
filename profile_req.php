<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "lost_found_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$email = isset($_GET['email']) ? $_GET['email'] : null;

if ($email) {
    $sql = "SELECT id, Fname, Lname, email, dept, contactNo FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode($row);
    } else {
        echo json_encode(array("error" => "User not found"));
    }

    $stmt->close();
} else {
    echo json_encode(array("error" => "No email parameter provided"));
}

$conn->close();
?>
