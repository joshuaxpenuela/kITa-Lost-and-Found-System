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

// Get filter, search, and category parameters
$filter = isset($_GET['filter']) ? $_GET['filter'] : '';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$search_category = isset($_GET['search_category']) ? $_GET['search_category'] : 'item_name';

// Predefined item categories (you can modify this list)
$item_categories = [
    'ID', 
    'Gadgets/Electronics', 
    'Tumbler', 
    'Accessories', 
    'Documents', 
    'Wallet', 
    'Umbrella', 
    'Money',
    'Bag',
    'Jewelries',
    'Clothes',
    'ATM Cards',
    'School Supplies',
    'Others'
];

// Build where clause
$where_clause = "WHERE 1=1";

if ($filter === 'pending') {
    $where_clause .= " AND remark = 'Pending'";
} elseif ($filter === 'approved_unclaimed') {
    $where_clause .= " AND status = 'Unclaimed' AND remark = 'Approved'";
} else {
    $where_clause .= " AND status = 'Unclaimed' AND remark != 'Reject'";
}

if (!empty($search)) {
    $search = mysqli_real_escape_string($conn, $search);
    
    // Dynamic search based on selected category
    switch ($search_category) {
        case 'item_name':
            $where_clause .= " AND item_name LIKE '%$search%'";
            break;
        case 'location_found':
            $where_clause .= " AND location_found LIKE '%$search%'";
            break;
        case 'item_category':
            $where_clause .= " AND item_category LIKE '%$search%'"; // Use LIKE for category search
            break;
        case 'date_found':
            $where_clause .= " AND report_date = '$search'";
            break;
        case 'other_details':
            $where_clause .= " AND other_details LIKE '%$search%'";
            break;
        case 'reported_by':
            $where_clause .= " AND (Fname LIKE '%$search%' OR Lname LIKE '%$search%')";
            break;
        default:
            $where_clause .= " AND item_name LIKE '%$search%'";
    }
}

// ... (rest of the script)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['mark_all_read'])) {
        // Mark all as read
        $update_query = "UPDATE reported_items SET view_status = 1 WHERE view_status = 0";
        if (mysqli_query($conn, $update_query)) {
            $_SESSION['message'] = "All items marked as read!";
        } else {
            $_SESSION['message'] = "Error marking items as read: " . mysqli_error($conn);
        }
    } elseif (isset($_POST['bulk_save'])) {
        // Handle bulk updates
        $items = $_POST['items'];
        $successful_updates = 0;
        $rejected_items = 0;
        $invalid_status_changes = 0;

        // Start a transaction for better error handling
        mysqli_begin_transaction($conn);

        try {
            foreach ($items as $id_item => $details) {
                $status = mysqli_real_escape_string($conn, $details['status']);
                $remark = mysqli_real_escape_string($conn, $details['remark']);

                // Validate status change
                $check_query = "SELECT status, remark FROM reported_items WHERE id_item = '$id_item'";
                $check_result = mysqli_query($conn, $check_query);
                $item = mysqli_fetch_assoc($check_result);

                // Additional validation for status change
                if ($status == 'Claimed' && $item['remark'] !== 'Pending') {
                    $invalid_status_changes++;
                    continue;
                }

                if ($item['status'] != $status || $item['remark'] != $remark) {
                    if ($remark == 'Reject') {
                        // Delete rejected items
                        $delete_query = "DELETE FROM reported_items WHERE id_item = '$id_item'";
                        if (mysqli_query($conn, $delete_query)) {
                            $rejected_items++;
                        }
                    } else {
                        // Update item
                        $update_query = "UPDATE reported_items SET status = '$status', remark = '$remark' WHERE id_item = '$id_item'";
                        if (mysqli_query($conn, $update_query)) {
                            $successful_updates++;
                        }
                    }
                }
            }

            // Commit the transaction
            mysqli_commit($conn);

            // Set session message
            $_SESSION['message'] = "Save All Changes completed. $successful_updates items updated, $rejected_items items rejected.";
            if ($invalid_status_changes > 0) {
                $_SESSION['message'] .= " $invalid_status_changes items could not be changed to Claimed due to invalid remark.";
            }
        } catch (Exception $e) {
            // Rollback the transaction in case of any error
            mysqli_rollback($conn);
            $_SESSION['message'] = "Error in bulk update: " . $e->getMessage();
        }
    } elseif (isset($_POST['id_item'])) {
        // Handle individual item updates with validation
        $id_item = mysqli_real_escape_string($conn, $_POST['id_item']);
        $status = mysqli_real_escape_string($conn, $_POST['status']);
        $remark = mysqli_real_escape_string($conn, $_POST['remark']);

        $check_query = "SELECT status, remark FROM reported_items WHERE id_item = '$id_item'";
        $check_result = mysqli_query($conn, $check_query);
        $item = mysqli_fetch_assoc($check_result);

        // Validate status change and update remark if necessary
        if ($status == 'Claimed' && $item['remark'] == 'Pending') {
            $update_query = "UPDATE reported_items SET status = '$status', remark = 'Approved' WHERE id_item = '$id_item'";
        } else {
            $update_query = "UPDATE reported_items SET status = '$status', remark = '$remark' WHERE id_item = '$id_item'";
        }

        if (mysqli_query($conn, $update_query)) {
            $_SESSION['message'] = "Item updated successfully!";
        } else {
            $_SESSION['message'] = "Error updating item: " . mysqli_error($conn);
        }

        header("Location: " . $_SERVER['PHP_SELF']);
        exit();

        if ($item['status'] != $status || $item['remark'] != $remark) {
            if ($remark == 'Reject') {
                $delete_query = "DELETE FROM reported_items WHERE id_item = '$id_item'";
                if (mysqli_query($conn, $delete_query)) {
                    $_SESSION['message'] = "Item rejected and removed!";
                } else {
                    $_SESSION['message'] = "Error deleting item: " . mysqli_error($conn);
                }
            } else {
                $query = "UPDATE reported_items SET status = '$status', remark = '$remark' WHERE id_item = '$id_item'";
                if (mysqli_query($conn, $query)) {
                    $_SESSION['message'] = "Item updated successfully!";
                } else {
                    $_SESSION['message'] = "Error updating item: " . mysqli_error($conn);
                }
            }
        } else {
            $_SESSION['message'] = "No changes made.";
        }
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Modify the query to prioritize pending items
$query = "
    (SELECT * FROM reported_items 
     $where_clause AND remark = 'Pending' 
     ORDER BY report_date DESC, report_time DESC)
    UNION ALL
    (SELECT * FROM reported_items 
     $where_clause AND remark != 'Pending' 
     ORDER BY report_date DESC, report_time DESC)
";

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
    <title>Unclaimed Items</title>
    <link rel="icon" href="images/kitaoldlogo.png" type="img/png">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="fontawesome/css/all.min.css">
    <script src="js/bootstrap.bundle.min.js"></script>
    <script>
    function handleSearch(event) {
        event.preventDefault();
        const searchCategory = document.getElementById('searchCategory').value;
        let searchValue = '';

        // Determine search value based on category
        if (searchCategory === 'item_category') {
            // Use dropdown for item category
            searchValue = document.getElementById('itemCategoryDropdown').value;
        } else if (searchCategory === 'date_found') {
            // Use date input
            searchValue = document.getElementById('searchInput').value;
        } else {
            // Use text input for other categories
            searchValue = document.getElementById('searchInput').value;
        }

        // Construct URL with search parameters
        let url = '<?php echo $_SERVER['PHP_SELF']; ?>?';
        
        // Only add search parameters if there's a value
        if (searchValue.trim() !== '') {
            url += `search=${encodeURIComponent(searchValue)}&`;
            url += `search_category=${encodeURIComponent(searchCategory)}`;
        }

        // Redirect to the constructed URL
        window.location.href = url;
    }

    document.addEventListener('DOMContentLoaded', function() {
        const searchCategory = document.getElementById('searchCategory');
        const searchInputContainer = document.getElementById('searchInputContainer');
        const searchInput = document.getElementById('searchInput');

        function updateSearchField() {
            // Clear previous content
            searchInputContainer.innerHTML = '';

            if (searchCategory.value === 'item_category') {
                // Create dropdown for item categories
                const dropdown = document.createElement('select');
                dropdown.id = 'itemCategoryDropdown';
                dropdown.className = 'form-control';

                // Populate dropdown with categories
                const categories = <?php echo json_encode($item_categories); ?>;
                categories.forEach(category => {
                    const option = document.createElement('option');
                    option.value = category;
                    option.textContent = category;
                    dropdown.appendChild(option);
                });

                searchInputContainer.appendChild(dropdown);
            } else if (searchCategory.value === 'date_found') {
                // Date input
                searchInput.type = 'date';
                searchInputContainer.appendChild(searchInput);
            } else {
                // Text input
                searchInput.type = 'text';
                searchInput.placeholder = 'Search for items...';
                searchInputContainer.appendChild(searchInput);
            }
        }

        // Initial setup and change event
        searchCategory.addEventListener('change', updateSearchField);
        updateSearchField();
    });
    function applyFilter(filter) {
            const url = '<?php echo $_SERVER['PHP_SELF']; ?>?filter=' + filter;
            window.location.href = url;
        }

        function clearFilters() {
            window.location.href = '<?php echo $_SERVER['PHP_SELF']; ?>';
        }

        function showBulkSaveModal() {
            // Check if any changes were made
            const changedRows = document.querySelectorAll('tr.row-changed');
            if (changedRows.length === 0) {
                alert('No changes to save.');
                return;
            }

            const bulkSaveModal = new bootstrap.Modal(document.getElementById('bulkSaveModal'));
            bulkSaveModal.show();
        }

        function prepareBulkSave() {
            const form = document.getElementById('bulkSaveForm');
            const changedRows = document.querySelectorAll('tr.row-changed');

            // Client-side validation for status change
            for (const row of changedRows) {
                const statusSelect = row.querySelector('select[name="status"]');
                const remarkSelect = row.querySelector('select[name="remark"]');

                // Check if trying to change status to Claimed
                if (statusSelect.value === 'Claimed' && remarkSelect.value !== 'Pending') {
                    alert('Cannot change status to Claimed. Item must have "Pending" remark.');
                    return false;
                }
            }

            changedRows.forEach(row => {
                const itemId = row.querySelector('input[name="id_item"]').value;
                const statusSelect = row.querySelector('select[name="status"]');
                const remarkSelect = row.querySelector('select[name="remark"]');

                // Create hidden inputs for bulk save form
                const statusInput = document.createElement('input');
                statusInput.type = 'hidden';
                statusInput.name = `items[${itemId}][status]`;
                statusInput.value = statusSelect.value;
                form.appendChild(statusInput);

                const remarkInput = document.createElement('input');
                remarkInput.type = 'hidden';
                remarkInput.name = `items[${itemId}][remark]`;
                remarkInput.value = remarkSelect.value;
                form.appendChild(remarkInput);
            });

            return true;
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Track row changes
            const rows = document.querySelectorAll('table tbody tr');
            rows.forEach(row => {
                const statusSelect = row.querySelector('select[name="status"]');
                const remarkSelect = row.querySelector('select[name="remark"]');

                // Disable "Claimed" option for status if remark is not "Pending"
                if (remarkSelect && remarkSelect.value !== 'Pending') {
                    const claimedOption = statusSelect.querySelector('option[value="Claimed"]');
                    if (claimedOption) {
                        claimedOption.disabled = true;
                    }
                }

                // Add event listeners to track changes
                const selects = row.querySelectorAll('select');
                selects.forEach(select => {
                    select.addEventListener('change', function() {
                        // Additional validation for status change
                        if (statusSelect.value === 'Claimed' && remarkSelect.value !== 'Pending') {
                            alert('Cannot change status to Claimed. Item must have "Pending" remark.');
                            // Reset to original values
                            statusSelect.value = row.dataset.originalStatus || 'Unclaimed';
                            return;
                        }

                        row.classList.add('row-changed');
                        row.style.backgroundColor = '#f0f0f0';
                    });
                });
            });
        });
    </script>
</head>
<body>
    <?php include "sidebar.php"; ?>

    <div class="content">
        <div class="container-fluid">
        <h1 class="mb-4">Unclaimed Items</h1>
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <!-- Filter Buttons -->
                <div class="filter-buttons">
                    <button class="btn <?php echo $filter === '' ? 'btn-primary active' : 'btn-outline-primary'; ?>" onclick="applyFilter('')">All Items</button>
                    <button class="btn <?php echo $filter === 'pending' ? 'btn-success active' : 'btn-outline-success'; ?>" onclick="applyFilter('pending')">Pending Requests</button>
                    <button class="btn <?php echo $filter === 'approved_unclaimed' ? 'btn-secondary active' : 'btn-outline-secondary'; ?>" onclick="applyFilter('approved_unclaimed')">Unclaimed & Approved</button>
                </div>

                <div>
                    <!-- "Mark All as Read" and "Bulk Save" Buttons -->
                    <form method="post" class="d-inline">
                        <button type="submit" name="mark_all_read" class="btn btn-success me-2">
                            <i class="fas fa-envelope-open-text"></i> Mark All as Read
                        </button>
                    </form>
                    <button onclick="showBulkSaveModal()" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save All Changes
                    </button>
                </div>
            </div>

            <!-- Bulk Save Confirmation Modal -->
            <div class="modal fade" id="bulkSaveModal" tabindex="-1" aria-labelledby="bulkSaveModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="bulkSaveModalLabel">Confirm Save Changes</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            Are you sure you want to save changes for all modified items?
                        </div>
                        <div class="modal-footer">
                            <form id="bulkSaveForm" method="post" onsubmit="return prepareBulkSave()">
                                <input type="hidden" name="bulk_save" value="1">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Confirm Save</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
    
    <!-- Search form -->
    <div class="search-filters mb-3">
        <form id="searchForm" onsubmit="handleSearch(event)" class="row g-1 align-items-center">
            <div class="col-md-3">
                <div class="form-group">
                    <select id="searchCategory" class="form-select">
                        <option value="item_name" <?php echo $search_category == 'item_name' ? 'selected' : ''; ?>>Item Name</option>
                        <option value="location_found" <?php echo $search_category == 'location_found' ? 'selected' : ''; ?>>Location Found</option>
                        <option value="item_category" <?php echo $search_category == 'item_category' ? 'selected' : ''; ?>>Item Category</option>
                        <option value="date_found" <?php echo $search_category == 'date_found' ? 'selected' : ''; ?>>Date Found</option>
                        <option value="other_details" <?php echo $search_category == 'other_details' ? 'selected' : ''; ?>>Other Details</option>
                        <option value="reported_by" <?php echo $search_category == 'reported_by' ? 'selected' : ''; ?>>Reported By</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div id="searchInputContainer">
                    <input type="text" id="searchInput" class="form-control" 
                        placeholder="Search for items..." 
                        value="<?php echo htmlspecialchars($search); ?>">
                </div>
            </div>
            <div class="col-md-3">
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary me-1">
                        <i class="fas fa-search"></i> Search
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="clearFilters()">
                        <i class="fas fa-times"></i> Clear Filters
                    </button>
                </div>
            </div>
        </form>
    </div>
    <div class="card1">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
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

                                <!-- Modal for each item (same as previous script) -->
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
                                                        <p><strong>Reported By:</strong> <?php echo $row['Fname'] . ' ' . $row['Lname']; ?></p>
                                                        <p><strong>Email:</strong> <?php echo $row['email']; ?></p>
                                                        <p><strong>Contact No:</strong> <?php echo $row['contact_no']; ?></p>
                                                        <p><strong>College:</strong> <?php echo $row['dept_college']; ?></p>
                                                        <p><strong>Item Name:</strong> <?php echo $row['item_name']; ?></p>
                                                        <p><strong>Item Category:</strong> <?php echo $row['item_category']; ?></p>
                                                        <p><strong>Location Found:</strong> <?php echo $row['location_found']; ?></p>
                                                        <p><strong>Date Found:</strong> <?php echo date('M d, Y', strtotime($row['report_date'])); ?></p>
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
                        <?php if (mysqli_num_rows($result) == 0): ?>
                            <div class="alert alert-info text-center">
                                No items found matching the current filters.
                            </div>
                        <?php endif; ?>
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
        </div>
    </div>
    <script src="main.js"></script>
</body>
</html>