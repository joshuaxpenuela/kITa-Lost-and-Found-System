<?php
session_start();
include 'db.php';

// Check if the admin is logged in
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

$admin_username = $_SESSION['admin'];

// Fetch admin details
$stmt = $conn->prepare("SELECT * FROM admins WHERE username = ?");
$stmt->bind_param("s", $admin_username);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();

// Check if the admin record was found
if (!$admin) {
    echo "<script>alert('Admin not found.'); window.location.href = 'login.php';</script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_username = htmlspecialchars(trim($_POST['username']));
    
    // Modified password handling with validation
    if (!empty($_POST['password'])) {
        $validation = validatePasswordStrength($_POST['password']);
        if (!$validation['valid']) {
            echo "<script>alert('" . $validation['message'] . "');</script>";
        } else {
            $new_password = md5(trim($_POST['password']));
        }
    } else {
        $new_password = $admin['password'];
    }

    $profile_image = isset($admin['profile_image']) ? $admin['profile_image'] : '';

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
            $new_filename = 'admin_' . $admin['id'] . '_' . time() . '.' . $file_extension;
            $upload_path = $upload_dir . $new_filename;

            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $upload_path)) {
                // Delete old profile image if it exists
                if (!empty($admin['profile_image']) && file_exists($admin['profile_image'])) {
                    unlink($admin['profile_image']);
                }
                $profile_image = $upload_path;
            } else {
                echo "<script>alert('Failed to upload image.');</script>";
            }
        } else {
            echo "<script>alert('Invalid file type or size. Please upload a JPEG, PNG, or GIF file under 5MB.');</script>";
        }
    }

    // Update admin details
    $sql = "UPDATE admins SET username = ?, password = ?";
    $types = "ss";
    $params = array($new_username, $new_password);

    if ($profile_image !== '') {
        $sql .= ", profile_image = ?";
        $types .= "s";
        $params[] = $profile_image;
    }

    $sql .= " WHERE id = ?";
    $types .= "i";
    $params[] = $admin['id'];

    $update_stmt = $conn->prepare($sql);
    
    if ($update_stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }

    if (!$update_stmt->bind_param($types, ...$params)) {
        die("Error binding parameters: " . $update_stmt->error);
    }

    if ($update_stmt->execute()) {
        $_SESSION['admin'] = $new_username;
        echo "<script>alert('Profile updated successfully!'); window.location.href = 'profile.php';</script>";
    } else {
        echo "<script>alert('Failed to update profile: " . $update_stmt->error . "');</script>";
    }
    $update_stmt->close();
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($admin['username']); ?>'s Profile</title>
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
                        <h2 class="text-center mb-4"><?php echo htmlspecialchars($admin['username']); ?>'s Profile</h2>
                        <form method="POST" action="profile.php" enctype="multipart/form-data">
                            <div class="image-preview-container">
                                <label for="profile_image" class="profile-image-container" title="Click to change profile picture">
                                    <img src="<?php echo !empty($admin['profile_image']) ? htmlspecialchars($admin['profile_image']) : 'images/ic_profile3.png'; ?>" 
                                         class="profile-image" 
                                         id="preview-image">
                                    <div class="upload-overlay">
                                        <i class="bi bi-camera-fill"></i>
                                        <div class="upload-text">Click to change profile picture</div>
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
                                <input type="text" class="form-control" id="username" name="username" 
                                       value="<?php echo htmlspecialchars($admin['username']); ?>" required>
                            </div>
                            <div class="mb-4">
                                <label for="password" class="form-label">New Password (leave blank to keep current password)</label>
                                <input type="password" class="form-control" id="password" name="password" minlength="8">
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-success btn-lg px-5">Update Profile</button>
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