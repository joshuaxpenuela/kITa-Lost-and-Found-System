<?php
session_start();
@include 'db.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

$where_clause = "WHERE status = 'Unclaimed' AND remark = 'Approved' OR remark = 'Pending'";
if (!empty($search)) {
    $search = mysqli_real_escape_string($conn, $search);
    $where_clause .= " AND $search_category LIKE '%$search%'";
}

// Check if the form has been submitted to update status/remark
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['mark_all_read'])) {
    $id_item = mysqli_real_escape_string($conn, $_POST['id_item']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $remark = mysqli_real_escape_string($conn, $_POST['remark']);

    // Fetch the current status and remark of the item
    $check_query = "SELECT status, remark FROM reported_items WHERE id_item = '$id_item'";
    $check_result = mysqli_query($conn, $check_query);
    $item = mysqli_fetch_assoc($check_result);

    // Compare with the current values and only update if there's a change
    if ($item['status'] != $status || $item['remark'] != $remark) {
        if ($remark == 'Reject') {
            // If remark is set to Reject, delete the item from the database
            $delete_query = "DELETE FROM reported_items WHERE id_item = '$id_item'";
            if (mysqli_query($conn, $delete_query)) {
                $_SESSION['message'] = "Item rejected and removed from the database.";
            } else {
                $_SESSION['message'] = "Error deleting item: " . mysqli_error($conn);
            }
        } else {
            // Otherwise, update the item as before
            $query = "UPDATE reported_items SET status = '$status', remark = '$remark' WHERE id_item = '$id_item'";
            if (mysqli_query($conn, $query)) {
                $_SESSION['message'] = "Item updated successfully!";
            } else {
                $_SESSION['message'] = "Error updating item: " . mysqli_error($conn);
            }
        }
    } else {
        $_SESSION['message'] = "No changes were made.";
    }

    $update_query = "UPDATE reported_items SET view_status = 1 WHERE view_status = 0";
    if (mysqli_query($conn, $update_query)) {
        $_SESSION['message'] = "All items marked as read successfully!";
    } else {
        $_SESSION['message'] = "Error marking items as read: " . mysqli_error($conn);
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Search functionality
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$search_category = isset($_GET['search_category']) ? $_GET['search_category'] : 'item_name';

$where_clause = "WHERE status = 'Unclaimed' AND remark != 'Reject'"; // Only show unclaimed and non-rejected items
if (!empty($search)) {
    $search = mysqli_real_escape_string($conn, $search);
    $where_clause .= " AND $search_category LIKE '%$search%'";
}

// Query to fetch unclaimed and non-rejected items
$query = "SELECT * FROM reported_items $where_clause ORDER BY claim_date DESC, claim_time DESC, report_date DESC, report_time DESC";
$result = mysqli_query($conn, $query);

$query = "SELECT * FROM reported_items $where_clause ORDER BY remark = 'Pending', claim_date DESC, claim_time DESC";
$result_1 = mysqli_query($conn, $query);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unclaimed Items</title>
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

    <div class="content">
        <div class="container-fluid">
            <h1 class="mb-4">Manage Unclaimed Items</h1>

            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <?php 
                    echo $_SESSION['message'];
                    unset($_SESSION['message']); 
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- Search form -->
            <form action="" method="GET" class="mb-4">
                <div class="input-group">
                    <select name="search_category" class="form-select">
                        <option value="item_name" <?php echo $search_category == 'item_name' ? 'selected' : ''; ?>>Item Name</option>
                        <option value="location_found" <?php echo $search_category == 'location_found' ? 'selected' : ''; ?>>Location Found</option>
                        <option value="item_category" <?php echo $search_category == 'item_category' ? 'selected' : ''; ?>>Item Category</option>
                    </select>
                    <input type="text" class="form-control" placeholder="Search for items..." name="search" value="<?php echo htmlspecialchars($search); ?>">
                    <button class="btn btn-primary" type="submit">Search</button>
                    <?php if (!empty($search)): ?>
                        <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn btn-secondary">Reset</a>
                    <?php endif; ?>
                </div>
            </form>
            <form action="" method="POST" class="mb-4">
                <button type="submit" name="mark_all_read" class="btn btn-primary">
                    Mark All as Read
                </button>
            </form>


            <div class="card1">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Image</th>
                                    <th>Reported By</th>
                                    <th>Item Name</th>
                                    <th>Date Found</th>
                                    <th>Location Found</th>
                                    <th>Status</th>
                                    <th>Remarks</th>
                                    <th>Action</th>
                                    <th>Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td><?php echo $row['id_item']; ?></td>
                                    <td>
                                        <?php if (!empty($row['img1'])): ?>
                                            <img src="../uploads/img_reported_items/<?php echo $row['img1']; ?>" 
                                                alt="Item Image" 
                                                class="item-thumbnail"
                                                data-bs-toggle="modal"
                                                data-bs-target="#imageModal<?php echo $row['id_item']; ?>">
                                        <?php else: ?>
                                            <div class="text-center">No Image</div>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo $row['Fname'] . ' ' . $row['Lname']; ?></td>
                                    <td><?php echo $row['item_name']; ?></td>
                                    <td><?php echo date('M d, Y', strtotime($row['report_date'])); ?></td>
                                    <td><?php echo $row['location_found']; ?></td>

                                    <form method="post">
                                        <input type="hidden" name="id_item" value="<?php echo $row['id_item']; ?>">
                                        
                                        <td>
                                            <select name="status" class="form-select form-select-sm">
                                                <option value="Unclaimed" <?php if ($row['status'] == 'Unclaimed') echo 'selected'; ?>>Unclaimed</option>
                                                <option value="Claimed" <?php if ($row['status'] == 'Claimed') echo 'selected'; ?>>Claimed</option>
                                            </select>
                                        </td>

                                        <td>
                                            <select name="remark" class="form-select form-select-sm">
                                                <option value="Approved" <?php if ($row['remark'] == 'Approved') echo 'selected'; ?>>Approved</option>
                                                <option value="Unapproved" <?php if ($row['remark'] == 'Unapproved') echo 'selected'; ?>>Unapproved</option>
                                                <option value="Pending" <?php if ($row['remark'] == 'Pending') echo 'selected'; ?>>Pending</option>
                                                <option value="Reject">Reject</option>
                                            </select>
                                        </td>

                                        <td>
                                            <button type="submit" class="btn btn-success btn-sm">Save</button>
                                        </td>
                                    </form>

                                    <td>
                                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#itemModal<?php echo $row['id_item']; ?>">
                                            View
                                        </button>
                                    </td>
                                </tr>

                                <!-- Modal for each item -->
                                <div class="modal fade" id="itemModal<?php echo $row['id_item']; ?>" tabindex="-1" aria-labelledby="itemModalLabel<?php echo $row['id_item']; ?>" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="itemModalLabel<?php echo $row['id_item']; ?>">Item Details</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <p><strong>ID:</strong> <?php echo $row['id_item']; ?></p>
                                                        <p><strong>Reported By:</strong> <?php echo $row['Fname'] . ' ' . $row['Lname']; ?></p>
                                                        <p><strong>Email:</strong> <?php echo $row['email']; ?></p>
                                                        <p><strong>Contact No:</strong> <?php echo $row['contact_no']; ?></p>
                                                        <p><strong>College:</strong> <?php echo $row['dept_college']; ?></p>
                                                        <p><strong>Item Name:</strong> <?php echo $row['item_name']; ?></p>
                                                        <p><strong>Item Category:</strong> <?php echo $row['item_category']; ?></p>
                                                        <p><strong>Location Found:</strong> <?php echo $row['location_found']; ?></p>
                                                        <p><strong>Date Claimed:</strong> <?php echo date('M d, Y', strtotime($row['report_date'])); ?></p>
                                                        <p><strong>Time:</strong> <?php echo date('h:i A', strtotime($row['report_time'])); ?></p>
                                                        <p><strong>Other Details:</strong> <?php echo $row['other_details']; ?></p>
                                                        <p><strong>Status:</strong> <?php echo $row['status']; ?></p>
                                                        <p><strong>Remark:</strong> <?php echo $row['remark']; ?></p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <!-- Carousel -->
                                                        <div id="carouselItem<?php echo $row['id_item']; ?>" class="carousel slide" data-bs-ride="carousel">
                                                            <div class="carousel-inner">
                                                                <?php 
                                                                $activeSet = false;
                                                                for ($i = 1; $i <= 5; $i++): 
                                                                    if (!empty($row["img$i"])): ?>
                                                                        <div class="carousel-item <?php echo !$activeSet ? 'active' : ''; ?>">
                                                                            <img src="../uploads/img_reported_items/<?php echo $row["img$i"]; ?>" class="d-block w-100" alt="Image <?php echo $i; ?>">
                                                                        </div>
                                                                        <?php 
                                                                        $activeSet = true;
                                                                    endif; 
                                                                endfor; ?>
                                                            </div>
                                                            <!-- Carousel Controls -->
                                                            <button class="carousel-control-prev" type="button" data-bs-target="#carouselItem<?php echo $row['id_item']; ?>" data-bs-slide="prev">
                                                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                                                <span class="visually-hidden">Previous</span>
                                                            </button>
                                                            <button class="carousel-control-next" type="button" data-bs-target="#carouselItem<?php echo $row['id_item']; ?>" data-bs-slide="next">
                                                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                                                <span class="visually-hidden">Next</span>
                                                            </button>
                                                        </div>
                                                        <!-- End of Carousel -->
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
    document.querySelector('input[name="search"]').addEventListener('input', function(e) {
        if (this.value.trim() === '') {
            window.location.href = '<?php echo $_SERVER['PHP_SELF']; ?>';
        }
    });
    </script>
    <script src="main.js"></script>
</body>
</html>