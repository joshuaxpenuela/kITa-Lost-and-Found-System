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
    $receiver_id = $_POST['receiver_id'];
    $created_at = date('Y-m-d H:i:s');

    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['image']['tmp_name'];
        $fileName = $_FILES['image']['name'];
        $uploadPath = "uploads/img_messages/" . $fileName;

        // Move the uploaded file to the designated path
        if (move_uploaded_file($fileTmpPath, $uploadPath)) {
            // Insert the image into user_message table
            $stmt = $conn->prepare("INSERT INTO user_message (sender_id, receiver_id, media_url, created_at) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iiss", $sender_id, $receiver_id, $uploadPath, $created_at);

            if ($stmt->execute()) {
                $response['success'] = true;
            } else {
                $response['success'] = false;
                $response['error'] = 'Database insert failed';
            }
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
