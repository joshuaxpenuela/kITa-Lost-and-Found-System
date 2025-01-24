<?php
session_start();
@include 'db.php';
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About kITa</title>
    <link rel="icon" href="images/kitaoldlogo.png" type="img/png">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="fontawesome/css/all.min.css">
    <script src="js/bootstrap.bundle.min.js"></script>    
</head>
<body>
    <?php
        include "sidebar.php";
    ?>
    <div class="content center-container mt-5 pt-5">
        <h2 class="about-title">About kITa: Lost and Found System</h2>
        <p class="about-paragraph">
            kITa is an innovative Android-based application and web-based platform that aims to enhance the efficiency of lost and found management. The platform allows users to report lost items and found items easily. It provides a seamless interface for both users and administrators, ensuring quick and accurate item recovery. kITa utilizes advanced technologies to match lost items with their rightful owners and provides real-time notifications to keep users updated. The system is designed to be user-friendly, making it accessible to people of all ages. Overall, kITa aims to revolutionize the way lost and found items are managed, making the process more efficient and effective.
        </p>
        
    </div>
    <br><br>
    <script src="main.js"></script>
</body>
</html>
