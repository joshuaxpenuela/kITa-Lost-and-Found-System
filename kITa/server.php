<?php
session_start();
include 'db.php';

// Check if user is already logged in via cookie
if (!isset($_SESSION['admin']) && isset($_COOKIE['admin'])) {
    $username = $_COOKIE['admin'];
    // Verify the username from the cookie
    $stmt = $conn->prepare("SELECT * FROM admins WHERE username = ? AND _status = 'enable'");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows > 0) {
        $_SESSION['admin'] = $username;
        header("Location: admin_dashboard.php");
        exit();
    }
    $stmt->close();
}

// Login functionality
if (isset($_POST['login'])) {
    $username = htmlspecialchars(trim($_POST['username']));
    $password = htmlspecialchars(trim($_POST['password']));

    // Use a more secure hashing method in production, like password_hash
    $hashed_password = md5($password); // Consider using password_hash and password_verify in a real application

    $stmt = $conn->prepare("SELECT * FROM admins WHERE username = ? AND password = ? AND _status = 'enable'");
    $stmt->bind_param("ss", $username, $hashed_password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $admin = $result->fetch_assoc();
        $_SESSION['admin'] = $username;
        $_SESSION['admin_level'] = $admin['admin_level'];

        if (isset($_POST['remember'])) {
            setcookie('admin_username', $username, time() + 3600, "/"); // Set cookie for 1 hour
            setcookie('admin_password', $password, time() + 3600, "/"); // Set cookie for 1 hour
        } else {
            // Clear cookies if remember me is not checked
            setcookie('admin_username', '', time() - 3600, "/");
            setcookie('admin_password', '', time() - 3600, "/");
        }

        echo "<script>
            alert('You are now logged in, Admin $username');
            window.location.href = 'admin_dashboard.php';
        </script>";
        exit();
    } else {
        // Check if the account is disabled
        $stmt = $conn->prepare("SELECT * FROM admins WHERE username = ? AND password = ? AND _status = 'disable'");
        $stmt->bind_param("ss", $username, $hashed_password);
        $stmt->execute();
        $disable_result = $stmt->get_result();

        if ($disable_result && $disable_result->num_rows > 0) {
            $error = "Your account is disabled. Please contact the administrator.";
        } else {
            $error = "Invalid login credentials.";
        }

        header("Location: login.php?error=" . urlencode($error));
        exit();
    }
    $stmt->close();
}

// Add new admin - Only allow super_admin to add new admins
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_admin'])) {
    // Check if the logged-in user is a super_admin
    if (!isset($_SESSION['admin_level']) || $_SESSION['admin_level'] !== 'super_admin') {
        echo "<script>
            alert('You do not have permission to add new administrators.');
            window.location.href = 'admin_dashboard.php';
        </script>";
        exit();
    }

    // Sanitize user input
    $username = htmlspecialchars(trim($_POST['username']));
    $password = htmlspecialchars(trim($_POST['password']));
    $admin_level = htmlspecialchars(trim($_POST['admin_level'])); // Allow specifying admin level

    // Hash the password using MD5
    $hashed_password = md5($password);

    // Prepare the SQL statement to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO admins (username, password, admin_level) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $hashed_password, $admin_level);

    if ($stmt->execute()) {
        echo "<script>
            alert('Registration successful. New Administrator Added!');
            window.location.href = 'admin_dashboard.php';
        </script>";
        exit();
    } else {
        $error = "Registration failed. Please try again.";
    }

    $stmt->close();
}
// For Dashboard
// Only execute these queries if the user is logged in
if (isset($_SESSION['admin'])) {
    // Fetch total lost items count
    $totalLostItemsQuery = "SELECT COUNT(*) as count FROM reported_items";
    $totalLostItemsResult = mysqli_query($conn, $totalLostItemsQuery);
    if (!$totalLostItemsResult) {
        die("Error executing query: " . mysqli_error($conn));
    }
    $totalLostItems = mysqli_fetch_assoc($totalLostItemsResult)['count'];

    // Fetch total found items count
    $totalFoundItemsQuery = "SELECT COUNT(*) as count FROM reported_items";
    $totalFoundItemsResult = mysqli_query($conn, $totalFoundItemsQuery);
    if (!$totalFoundItemsResult) {
        die("Error executing query: " . mysqli_error($conn));
    }
    $totalFoundItems = mysqli_fetch_assoc($totalFoundItemsResult)['count'];

    // Fetch most lost and found items
    $mostLostFoundItemsQuery = "
        SELECT item_name, 
               (SELECT COUNT(*) FROM reported_items WHERE reported_items.item_name = items.item_name) as lost_count,
               (SELECT COUNT(*) FROM reported_items WHERE reported_items.item_name = items.item_name) as found_count
        FROM (
            SELECT item_name FROM reported_items
            UNION 
            SELECT item_name FROM reported_items
        ) as items
        GROUP BY item_name
        ORDER BY lost_count DESC, found_count DESC
        LIMIT 10";
    $mostLostFoundItemsResult = mysqli_query($conn, $mostLostFoundItemsQuery);
    if (!$mostLostFoundItemsResult) {
        die("Error executing query: " . mysqli_error($conn));
    }
}

?>