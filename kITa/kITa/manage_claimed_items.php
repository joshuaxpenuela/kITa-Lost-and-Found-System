<?php
session_start();
@include 'db.php';
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Search functionality
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$search_category = isset($_GET['search_category']) ? $_GET['search_category'] : 'item_name';

$where_clause = "WHERE status = 'Claimed' AND remark = 'Approved'";
if (!empty($search)) {
    $search = mysqli_real_escape_string($conn, $search);
    $where_clause .= " AND $search_category LIKE '%$search%'";
}

// Query to get claimed and approved items from reported_items table
$query = "SELECT *, validID FROM reported_items 
          $where_clause
          ORDER BY claim_date DESC, claim_time DESC";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Claimed Items</title>
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
            <h1 class="mb-4">Claimed Items</h1>

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
                                    <th>Date Claimed</th>
                                    <th>Location Found</th>
                                    <th>Details</th>
                                    <th>Action</th>
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
                                            <img src="path/to/no-image-placeholder.png" alt="No Image" class="item-thumbnail">
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo $row['Fname'] . ' ' . $row['Lname']; ?></td>
                                    <td><?php echo $row['item_name']; ?></td>
                                    <td><?php echo date('M d, Y', strtotime($row['claim_date'])); ?></td>
                                    <td><?php echo $row['location_found']; ?></td>
                                    <td>
                                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#itemModal<?php echo $row['id_item']; ?>">
                                            View
                                        </button>
                                    </td>
                                    <!-- In your claimed items table, modify the "Details" column to include the report button -->
                                    <td>
                                        <a href="generate_accomplishment_report.php?id=<?php echo $row['id_item']; ?>" class="btn btn-success btn-sm">
                                            <i class="fas fa-file-pdf"></i> Report
                                        </a>
                                    </td>
                                    <!-- In your claimed items table, modify the "Details" column to include the report button -->
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
                                                        <p><strong>Claimed By:</strong> <?php echo $row['claim_Fname'] . ' ' . $row['claim_Lname']; ?></p>
                                                        <p><strong>Email:</strong> <?php echo $row['claim_email']; ?></p>
                                                        <p><strong>Contact No:</strong> <?php echo $row['claim_contact']; ?></p>
                                                        <p><strong>Item Name:</strong> <?php echo $row['item_name']; ?></p>
                                                        <p><strong>Item Category:</strong> <?php echo $row['item_category']; ?></p>
                                                        <p><strong>Location Found:</strong> <?php echo $row['location_found']; ?></p>
                                                        <p><strong>Date Claimed:</strong> <?php echo date('M d, Y', strtotime($row['claim_date'])); ?></p>
                                                        <p><strong>Time:</strong> <?php echo date('h:i A', strtotime($row['claim_time'])); ?></p>
                                                        <p><strong>Claimer's Description:</strong> <?php echo $row['claim_desc']; ?></p>
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
                                                                // First, add the valid ID image if it exists
                                                                if (!empty($row['validID'])): ?>
                                                                    <div class="carousel-item active">
                                                                        <img src="../uploads/img_valid_ids/<?php echo $row['validID']; ?>" class="d-block w-100" alt="Valid ID" style="object-fit: contain;">
                                                                        <div class="carousel-caption d-none d-md-block">
                                                                            <h5 class="bg-dark bg-opacity-50 p-2 rounded">Valid ID</h5>
                                                                        </div>
                                                                    </div>
                                                                    <?php 
                                                                    $activeSet = true;
                                                                endif;
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
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const links = document.querySelectorAll('.nav-link, .dropdown-item');
        const currentPath = window.location.pathname.split("/").pop();

        links.forEach(link => {
            if (link.getAttribute('href') === currentPath) {
                link.classList.add('active');
            }
        });
    });
</script>
</body>
</html>