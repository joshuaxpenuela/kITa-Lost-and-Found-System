<?php
header('Content-Type: application/json');
session_start();

// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "lost_found_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'error' => 'Connection failed: ' . $conn->connect_error]);
    exit();
}

// Get email and password from POST request
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

// Validate input
if (empty($email) || empty($password)) {
    echo json_encode(['success' => false, 'error' => 'Email and password are required']);
    exit();
}

// Prepare and execute the query to check the user's credentials
$sql = "SELECT id, Fname, Lname, email, contactNo, dept, password FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    
    // Check the password (assumes password is stored hashed in the database)
    if (password_verify($password, $user['password'])) {
        // Set session variables
        $_SESSION['user'] = $email;
        
        // Return user data along with success status
        echo json_encode([
            'success' => true,
            'id' => $user['id'],
            'Fname' => $user['Fname'],
            'Lname' => $user['Lname'],
            'email' => $user['email'],
            'contactNo' => $user['contactNo'],
            'dept' => $user['dept']
        ]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid password']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'User not found']);
}

// Close connection
$conn->close();
?>
