<?php
session_start();
@include 'db.php';
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

$current_admin_username = $_SESSION['admin'];

// Get current admin's details to check admin level
$admin_check_query = "SELECT admin_level FROM admins WHERE username = ?";
$stmt = $conn->prepare($admin_check_query);
$stmt->bind_param("s", $current_admin_username);
$stmt->execute();
$result = $stmt->get_result();
$admin_details = $result->fetch_assoc();
$current_admin_level = $admin_details['admin_level'];
$stmt->close();

// Handle admin status toggle
if (isset($_POST['toggle_status'])) {
    // Additional check to ensure only super admin can toggle status
    if ($current_admin_level !== 'admin') {
        $_SESSION['error'] = "You do not have permission to disable admins.";
        header("Location: manage_users.php");
        exit();
    }

    $admin_id = intval($_POST['admin_id']);
    $new_status = $_POST['new_status'];
    $update_query = "UPDATE admins SET _status = ? WHERE id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("si", $new_status, $admin_id);
    $stmt->execute();
    $stmt->close();
    header("Location: manage_users.php");
    exit();
}

// Get all departments for filter dropdown
$dept_query = "SELECT DISTINCT dept FROM users ORDER BY dept";
$dept_result = $conn->query($dept_query);
$departments = [];
while ($row = $dept_result->fetch_assoc()) {
    $departments[] = $row['dept'];
}

// Handle search and filter
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$dept_filter = isset($_GET['department']) ? $conn->real_escape_string($_GET['department']) : '';

// Modified admin query to sort by ID
$query_0 = "SELECT id, username, _status FROM admins ORDER BY id ASC";

// Modified user query with search, filter, and ID sorting
$query_1 = "SELECT id, Lname, Fname, email, dept, contactNo FROM users WHERE 1=1";
if ($search) {
    $query_1 .= " AND (Lname LIKE '%$search%' OR Fname LIKE '%$search%' OR email LIKE '%$search%')";
}
if ($dept_filter) {
    $query_1 .= " AND dept = '$dept_filter'";
}
$query_1 .= " ORDER BY id ASC";

$result_0 = $conn->query($query_0);
$result_1 = $conn->query($query_1);

if (!$result_0 || !$result_1) {
    die("Error executing queries: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users</title>
    <link rel="icon" href="images/kitaoldlogo.png" type="img/png">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="fontawesome/css/all.min.css">
    <script src="js/bootstrap.bundle.min.js"></script>
    <style>
        .search-filters {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .search-filters .form-group {
            margin-bottom: 0;
        }
    </style>
    <script>
        function confirmToggle(adminId, adminUsername, currentStatus) {
        // Check if disable button should be disabled
        const disableButton = document.getElementById(`disableBtn_${adminId}`);
        if (disableButton && disableButton.hasAttribute('disabled')) {
            alert('You do not have permission to disable admins.');
            return;
        }

        const action = currentStatus === 'disable' ? 'enable' : 'disable';
        if (confirm(`Are you sure you want to ${action} admin: ${adminUsername}?`)) {
            document.getElementById(`toggleForm_${adminId}_${currentStatus}`).submit();
        }
    }

        function handleSearch(event) {
            event.preventDefault();
            const searchForm = document.getElementById('searchForm');
            const searchQuery = document.getElementById('searchInput').value;
            const departmentFilter = document.getElementById('departmentFilter').value;
            
            let url = '<?php echo $_SERVER['PHP_SELF']; ?>?';
            if (searchQuery) {
                url += `search=${encodeURIComponent(searchQuery)}&`;
            }
            if (departmentFilter) {
                url += `department=${encodeURIComponent(departmentFilter)}`;
            }
            
            window.location.href = url;
        }

        function clearFilters() {
            window.location.href = '<?php echo $_SERVER['PHP_SELF']; ?>';
        }
    </script>
</head>
<body>
    <?php include "sidebar.php"; ?>
    <div class="content">
        <div class="container-fluid">
            <h2 class="mb-4">Manage Admins</h2>
                <?php 
            // Display error message if set
            if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger">
                    <?php 
                            echo htmlspecialchars($_SESSION['error']); 
                            unset($_SESSION['error']); 
                            ?>
                        </div>
                    <?php endif; ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result_0->fetch_assoc()): ?>
                        <?php if ($row['username'] != $current_admin_username): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['id']); ?></td>
                                <td><?php echo htmlspecialchars($row['username']); ?></td>
                                <td>
                                    <span class="badge <?php echo $row['_status'] == 'enable' ? 'bg-success' : 'bg-danger'; ?>">
                                        <?php echo $row['_status']; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($row['_status'] == 'enable'): ?>
                                        <form id="toggleForm_<?php echo $row['id']; ?>_enable" action="" method="post" style="display:inline;">
                                            <input type="hidden" name="admin_id" value="<?php echo $row['id']; ?>">
                                            <input type="hidden" name="new_status" value="disable">
                                            <input type="hidden" name="toggle_status" value="1">
                                            <button type="button" 
                                                id="disableBtn_<?php echo $row['id']; ?>"
                                                class="btn btn-danger btn-sm" 
                                                <?php echo $current_admin_level !== 'admin' ? 'disabled title="Only super admin can disable"' : ''; ?>
                                                onclick="confirmToggle(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars($row['username']); ?>', 'enable')">
                                                <i class="fas fa-times-circle"></i> Disable
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <form id="toggleForm_<?php echo $row['id']; ?>_disable" action="" method="post" style="display:inline;">
                                            <input type="hidden" name="admin_id" value="<?php echo $row['id']; ?>">
                                            <input type="hidden" name="new_status" value="enable">
                                            <input type="hidden" name="toggle_status" value="1">
                                            <button type="button" 
                                                class="btn btn-success btn-sm" 
                                                <?php echo $current_admin_level !== 'admin' ? 'disabled title="Only super admin can enable"' : ''; ?>
                                                onclick="confirmToggle(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars($row['username']); ?>', 'disable')">
                                                <i class="fas fa-check"></i> Enable
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        
        <div class="container-fluid">
            <h2>Manage Users</h2>
            
            <div class="search-filters">
                <form id="searchForm" onsubmit="handleSearch(event)" class="row g-3">
                    <div class="col-md-4">
                        <div class="form-group">
                            <input type="text" id="searchInput" class="form-control" 
                                placeholder="Search by name or email" 
                                value="<?php echo htmlspecialchars($search); ?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <select id="departmentFilter" class="form-select">
                                <option value="">All Departments</option>
                                <?php foreach ($departments as $dept): ?>
                                    <option value="<?php echo htmlspecialchars($dept); ?>" 
                                        <?php echo $dept_filter === $dept ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($dept); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-search"></i> Search
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="clearFilters()">
                            <i class="fas fa-times"></i> Clear Filters
                        </button>
                    </div>
                </form>
            </div>

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Last Name</th>
                        <th>First Name</th>
                        <th>Email</th>
                        <th>Department</th>
                        <th>Contact No</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result_1->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                            <td><?php echo htmlspecialchars($row['Lname']); ?></td>
                            <td><?php echo htmlspecialchars($row['Fname']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo htmlspecialchars($row['dept']); ?></td>
                            <td><?php echo htmlspecialchars($row['contactNo']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <?php $conn->close(); ?>
    <script src="main.js"></script>
</body>
</html>