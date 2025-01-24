<?php
session_start();
@include 'db.php';

// Check if the user is logged in as an admin
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

$admin_id = $_SESSION['admin']; // Admin's ID

// Handle message sending
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['send_message']) ) {
    $user_id = $_POST['user_id'];  // The user the admin is messaging
    $message = trim($_POST['message']);
    
    if (!empty($message)) {
        // Insert the message from admin to user
        $sql = "INSERT INTO admin_message (admin_id, user_id, message, sender_id, receiver_id) 
                VALUES (?, ?, ?, 0, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iisi", $admin_id, $user_id, $message, $user_id);  // sender_id = 0 (admin), receiver_id = user ID
        $stmt->execute();
    }
}

// Fetch all users with their last message
$sql = "SELECT u.id, u.Fname, u.Lname, u.email, 
        (SELECT message FROM admin_message
         WHERE (sender_id = u.id AND receiver_id = ?) OR (admin_id = ? AND user_id = u.id)
         ORDER BY created_at DESC LIMIT 1) as last_message,
        (SELECT created_at FROM admin_message
         WHERE (sender_id = u.id AND receiver_id = ?) OR (admin_id = ? AND user_id = u.id)
         ORDER BY created_at DESC LIMIT 1) as last_message_time
        FROM users u
        ORDER BY CASE WHEN last_message_time IS NULL THEN 1 ELSE 0 END, last_message_time DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iiii", $admin_id, $admin_id, $admin_id, $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$users = $result->fetch_all(MYSQLI_ASSOC);

// Function to get messages between admin and a selected user
function getMessages($conn, $admin_id, $user_id) {
    $sql = "SELECT m.id, m.sender_id, m.receiver_id, m.admin_id, m.user_id, m.message, m.created_at,
            CASE 
                WHEN m.sender_id = ? THEN 'user'  -- Message sent by user
                WHEN m.admin_id = ? THEN 'admin'  -- Message sent by admin
                ELSE 'unknown'
            END as sender_type
            FROM admin_message m
            WHERE (m.sender_id = ? AND m.receiver_id = ?) OR (m.admin_id = ? AND m.user_id = ?)
            ORDER BY m.created_at ASC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiiiii", $user_id, $admin_id, $user_id, $admin_id, $admin_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['mark_all_read'])) {
    $update_query = "UPDATE admin_message SET view_status = 1 WHERE view_status = 0 AND receiver_id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("i", $admin_id);
    
    if ($stmt->execute()) {
        $_SESSION['message'] = "All messages marked as read successfully!";
    } else {
        $_SESSION['message'] = "Error marking messages as read: " . $conn->error;
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages</title>
    <link rel="icon" href="images/kitaoldlogo.png" type="img/png">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="fontawesome/css/all.min.css">
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
</head>
<body>
    <?php
        include "sidebar.php";
    ?>
    <main class="content">
        <div class="row h-100">
            <div class="col-md-4">
                <h3>User's Messages</h3>
                <form action="" method="POST" class="mb-4">
                    <button type="submit" name="mark_all_read" class="btn btn-primary btn-sm">
                        Mark All as Read
                    </button>
                </form>
                <div class="user-list">
                    <?php foreach ($users as $user): ?>
                        <button class="user-select w-100 mb-2" data-user-id="<?php echo $user['id']; ?>">
                            <strong><?php echo htmlspecialchars($user['Fname'] . ' ' . $user['Lname']); ?></strong><br>
                            <small><?php echo htmlspecialchars($user['email']); ?></small><br>
                            <?php if ($user['last_message']): ?>
                                <small><?php echo htmlspecialchars(substr($user['last_message'], 0, 30)); ?>...</small>
                                <small class="text-muted d-block"><?php echo date('M d, Y H:i', strtotime($user['last_message_time'])); ?></small>
                            <?php else: ?>
                                <small class="text-muted">No messages yet</small>
                            <?php endif; ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="col-md-8 d-flex flex-column">
                <div id="messageContainer" class="message-container flex-grow-1">
                    <p class="text-center text-muted mt-5">Select a user to view messages</p>
                </div>

                <form id="messageForm" class="mt-3">
                    <div class="input-group">
                        <input type="text" id="messageInput" class="form-control" placeholder="Type your message..." required>
                        <button type="submit" class="btn btn-primary">Send</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
    <script>
        $(document).ready(function() {
            let currentUserId = null;

            $('.user-select').click(function() {
                $('.user-select').removeClass('active');
                $(this).addClass('active');
                currentUserId = $(this).data('user-id');
                loadMessages(currentUserId);
            });

            $('#messageForm').submit(function(e) {
                e.preventDefault();
                if (currentUserId) {
                    const message = $('#messageInput').val().trim();
                    if (message !== '') {
                        $.ajax({
                            url: 'send_message.php',
                            method: 'POST',
                            data: {
                                user_id: currentUserId,
                                message: message
                            },
                            dataType: 'json', // Expect JSON response
                            success: function(response) {
                                if (response.status === 'success') {
                                    $('#messageInput').val('');
                                    loadMessages(currentUserId);
                                } else {
                                    alert(response.message);
                                }
                            },
                            error: function() {
                                alert('Failed to send message');
                            }
                        });
                    }
                }
            });

            function loadMessages(userId) {
                $.get('get_messages.php', { user_id: userId }, function(data) {
                    $('#messageContainer').html(data);
                    $('#messageContainer').scrollTop($('#messageContainer')[0].scrollHeight);
                });
            }

            // Periodically refresh messages
            setInterval(function() {
                if (currentUserId) {
                    loadMessages(currentUserId);
                }
            }, 5000);
        });
    </script>
</body>
</html>