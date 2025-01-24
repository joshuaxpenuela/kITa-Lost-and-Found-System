<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "lost_found_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$response = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sender_id = $_POST['sender_id'];
    $created_at = date('Y-m-d H:i:s');

    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['image']['tmp_name'];
        $fileName = $_FILES['image']['name'];
        $uploadPath = "uploads/img_messages/" . $fileName;

        // Move the uploaded file to the designated path
        if (move_uploaded_file($fileTmpPath, $uploadPath)) {
            // Fetch all admin IDs from the admins table
            $admin_ids = array();
            $admin_query = "SELECT id FROM admins";
            $result = $conn->query($admin_query);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $admin_ids[] = $row['id'];
                }
            }

            // Insert a single entry to avoid duplicates
            $stmt = $conn->prepare("INSERT INTO admin_message (sender_id, receiver_id, media_url, created_at) VALUES (?, ?, ?, ?)");
            $success = true;
            $stmt->bind_param("iiss", $sender_id, $admin_ids[0], $uploadPath, $created_at);

            if (!$stmt->execute()) {
                $success = false;
            }

            $response['success'] = $success;
        } else {
            $response['success'] = false;
            $response['error'] = 'File upload failed';
        }
    } else {
        $response['success'] = false;
        $response['error'] = 'No file uploaded or file error';
    }
} else {
    $response['success'] = false;
}

echo json_encode($response);
$stmt->close();
$conn->close();
?>
