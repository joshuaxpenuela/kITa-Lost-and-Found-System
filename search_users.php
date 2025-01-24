<?php
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

$response = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $query = $_POST['query'];

    // Search for users whose first name or last name matches the query
    $sql = "SELECT id, Fname, Lname FROM users WHERE Fname LIKE ? OR Lname LIKE ?";
    $search_query = "%" . $query . "%";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $search_query, $search_query);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $users = array();

        while ($row = $result->fetch_assoc()) {
            $user = array(
                "id" => $row['id'],
                "Fname" => $row['Fname'],
                "Lname" => $row['Lname']
            );
            $users[] = $user;
        }

        $response['success'] = true;
        $response['users'] = $users;
    } else {
        $response['success'] = false;
        $response['message'] = "Failed to search users.";
    }

    $stmt->close();
} else {
    $response['success'] = false;
    $response['message'] = "Invalid request.";
}

$conn->close();
echo json_encode($response);
?>