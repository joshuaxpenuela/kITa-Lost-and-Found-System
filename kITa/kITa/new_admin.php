<?php
include 'db.php';
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

function validatePasswordStrength($password) {
    $length = strlen($password);
    
    if ($length < 8) {
        return [
            'strength' => 'weak',
            'message' => 'Password is too weak! Must be at least 8 characters.',
            'valid' => false
        ];
    } elseif ($length === 8) {
        return [
            'strength' => 'good',
            'message' => 'Password strength is good!',
            'valid' => true
        ];
    } else {
        return [
            'strength' => 'strong',
            'message' => 'Password strength is strong!',
            'valid' => true
        ];
    }
}

$error = $success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $password = mysqli_real_escape_string($conn, trim($_POST['password']));
    
    // Add password validation
    $validation = validatePasswordStrength($password);
    if (!$validation['valid']) {
        $error = $validation['message'];
    } else {
        $hashed_password = md5($password);
        $profile_image = '';

        // Handle image upload
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $max_size = 5 * 1024 * 1024; // 5MB
            
            if (in_array($_FILES['profile_image']['type'], $allowed_types) && 
                $_FILES['profile_image']['size'] <= $max_size) {
                
                $upload_dir = 'uploads/profile_images/';
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }

                $file_extension = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
                $new_filename = 'admin_' . time() . '.' . $file_extension;
                $upload_path = $upload_dir . $new_filename;

                if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $upload_path)) {
                    $profile_image = $upload_path;
                } else {
                    $error = "Failed to upload image.";
                }
            } else {
                $error = "Invalid file type or size. Please upload a JPEG, PNG, or GIF file under 5MB.";
            }
        }

        if (empty($error)) {
            $check_query = "SELECT * FROM admins WHERE username = ?";
            $stmt = $conn->prepare($check_query);
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $error = "Username already exists!";
            } else {
                $insert_query = "INSERT INTO admins (username, password, profile_image) VALUES (?, ?, ?)";
                $stmt = $conn->prepare($insert_query);
                $stmt->bind_param("sss", $username, $hashed_password, $profile_image);

                if ($stmt->execute()) {
                    $success = "New admin added successfully!";
                    echo "<script>
                        alert('New admin account has been successfully added.');
                        window.location.href = 'admin_dashboard.php';
                    </script>";
                    exit();
                } else {
                    $error = "Error adding admin: " . $conn->error;
                }
            }
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Admin User</title>
    <link rel="icon" href="images/kitaoldlogo.png" type="img/png">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="fontawesome/css/all.min.css">
    <script src="js/bootstrap.bundle.min.js"></script>
    <style>
    .password-strength-indicator {
        margin-top: -15px;
        margin-bottom: 15px;
    }
    </style>
    <script src="js/password-strength.js"></script>
</head>
<body>
    <?php
        include "sidebar.php";
    ?>
    <div class="content center-container mt-4 py-4">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="form-container">
                        <h2 class="text-center mb-4">Add New Admin</h2>
                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger" role="alert">
                                <?php echo $error; ?>
                            </div>
                        <?php endif; ?>
                        <form method="POST" action="new_admin.php" enctype="multipart/form-data">
                            <div class="image-preview-container mb-3">
                                <label for="profile_image" class="profile-image-container" title="Click to add profile picture">
                                    <img src="images/ic_profile3.png" 
                                          
                                         class="profile-image" 
                                         id="preview-image">
                                    <div class="upload-overlay">
                                        <i class="bi bi-camera-fill"></i>
                                        <div class="upload-text">Click to add profile picture</div>
                                    </div>
                                </label>
                                <input type="file" 
                                       class="form-control" 
                                       id="profile_image" 
                                       name="profile_image" 
                                       accept="image/jpeg,image/png,image/gif"
                                       onchange="previewImage(this)">
                                <div class="current-image-info" id="image-info"></div>
                            </div>
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required minlength="8">
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-success btn-lg px-5"> Register </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="main.js"></script>
</body>
</html>