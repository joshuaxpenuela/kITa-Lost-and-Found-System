<?php
// logout.php
session_start();

if (isset($_POST['confirm_logout'])) {
    $_SESSION = array();
    session_destroy();
    setcookie('admin', '', time() - 3600, '/');
    
    // Return JSON response for AJAX handling
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'redirect' => 'login.php']);
    exit();
}
?>