<?php
include 'server.php';

// Redirect if user is already logged in
if (isset($_SESSION['admin'])) {
    header("Location: admin_dashboard.php");
    exit();
}

// Check for error messages
$error = isset($_GET['error']) ? $_GET['error'] : '';

// Pre-fill the username if the cookie is set
$saved_username = isset($_COOKIE['admin_username']) ? $_COOKIE['admin_username'] : '';
$saved_password = isset($_COOKIE['admin_password']) ? $_COOKIE['admin_password'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>kITa: Lost and Found System</title>
    <link rel="icon" href="images/kitaoldlogo.png" type="img/png">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/styles.css">
    <style>
        body, html {
            height: 100%;
            margin: 0;
        }

        .bg {
            background: url('images/cvsu.jpg') no-repeat center center fixed;
            background-size: cover;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            filter: blur(4px);
        }

        .container {
            top: 50%;
            left: 50%;
            position: absolute;
            transform: translate(-50%, -50%);
            z-index: 1;
        }
        h2 {
            font-weight: bold;
        }
        .card {
            background-color: rgba(255, 255, 255, 0.6);
        }
        .logo {
            justify-content: center;
            align-items: center;
            display: flex;
            margin: 15px 0;
        }
        .logo img{
            max-width: 300px;
            height: auto;
        }
    </style>
</head>
<body>
    <div class="bg"></div>
    <section>
        <div class="container">
            <div class="row">
                <div class="col-12 col-sm-8 col-md-6 m-auto">
                    <div class="card border-10 shadow">
                        <div class="card-body">
                            <div class="logo">
                                <img src="images/new_kitalogo1.png" alt="kITa Logo">
                            </div>
                            <form method="POST" action="server.php">
                                <h2 class="text-center">Login</h2>
                                <?php if (!empty($error)) { echo '<div class="alert alert-danger text-center">' . htmlspecialchars($error) . '</div>'; } ?>
                                <div class="">
                                <input type="text" name="username" class="form-control my-3 py-2" placeholder="Enter Username" value="<?php echo htmlspecialchars($saved_username); ?>" required>
                                <input type="password" name="password" class="form-control my-3 py-2" placeholder="Enter Password" value="<?php echo htmlspecialchars($saved_password); ?>" required>
                                </div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" <?php if($saved_username) echo 'checked'; ?>>
                                    <label class="form-check-label" for="remember">Remember me</label>
                                </div>
                                <div class="text-center mt-3">
                                    <button type="submit" name="login" class="btn btn-success">Login</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>  
    </section>
</body>
</html>
