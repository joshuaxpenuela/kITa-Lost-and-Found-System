<?php
session_start();
require 'db.php';

// Check if user is not logged in
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_signatory'])) {
        $position = $_POST['position'];
        $name = $_POST['name'];
        $designation = $_POST['designation'];
        $display_order = $_POST['display_order'];

        $stmt = $conn->prepare("INSERT INTO signatories (position, name, designation, display_order) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $position, $name, $designation, $display_order);
        $stmt->execute();
        $stmt->close();
    }

    if (isset($_POST['update_signatory'])) {
        $id = $_POST['id'];
        $position = $_POST['position'];
        $name = $_POST['name'];
        $designation = $_POST['designation'];
        $display_order = $_POST['display_order'];

        $stmt = $conn->prepare("UPDATE signatories SET position = ?, name = ?, designation = ?, display_order = ? WHERE id = ?");
        $stmt->bind_param("sssii", $position, $name, $designation, $display_order, $id);
        $stmt->execute();
        $stmt->close();
    }

    if (isset($_POST['delete_signatory'])) {
        $id = $_POST['id'];
        $stmt = $conn->prepare("DELETE FROM signatories WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }

    // Redirect to prevent form resubmission
    header("Location: manage_signatories.php");
    exit();
}

// Fetch current signatories
$signatories_query = "SELECT * FROM signatories ORDER BY display_order";
$signatories_result = $conn->query($signatories_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Signatories</title>
    <link rel="icon" href="images/kitaoldlogo.png" type="img/png" sizes="48x48">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="fontawesome/css/all.min.css">
    <script src="js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <?php include "sidebar.php"; ?>
    
    <div class="content container-fluid">
        <div class="dashboard-header">
            <div class="d-flex justify-content-between align-items-center">
                <h2>Manage Signatories</h2>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Add/Edit Signatory</div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="id" id="edit-id">
                            <div class="form-group mb-2">
                                <label>Position</label>
                                <input type="text" class="form-control" name="position" id="edit-position" required>
                            </div>
                            <div class="form-group mb-2">
                                <label>Name</label>
                                <input type="text" class="form-control" name="name" id="edit-name" required>
                            </div>
                            <div class="form-group mb-2">
                                <label>Designation</label>
                                <input type="text" class="form-control" name="designation" id="edit-designation">
                            </div>
                            <div class="form-group mb-2">
                                <label>Display Order</label>
                                <input type="number" class="form-control" name="display_order" id="edit-display-order" required>
                            </div>
                            <div class="form-group mb-2">
                                <button type="submit" name="add_signatory" id="add-btn" class="btn btn-primary">Add Signatory</button>
                                <button type="submit" name="update_signatory" id="update-btn" class="btn btn-success" style="display:none;">Update Signatory</button>
                                <button type="button" id="cancel-btn" class="btn btn-secondary" style="display:none;" onclick="cancelEdit()">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Current Signatories</div>
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Position</th>
                                    <th>Name</th>
                                    <th>Designation</th>
                                    <th>Order</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($row = $signatories_result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['position']); ?></td>
                                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['designation'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($row['display_order']); ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-warning" onclick="editSignatory(
                                            <?php echo $row['id']; ?>, 
                                            '<?php echo htmlspecialchars(addslashes($row['position'])); ?>', 
                                            '<?php echo htmlspecialchars(addslashes($row['name'])); ?>', 
                                            '<?php echo htmlspecialchars(addslashes($row['designation'] ?? '')); ?>', 
                                            <?php echo $row['display_order']; ?>
                                        )">Edit</button>
                                        <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this signatory?');">
                                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                            <button type="submit" name="delete_signatory" class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    function editSignatory(id, position, name, designation, displayOrder) {
        // Populate form for editing
        document.getElementById('edit-id').value = id;
        document.getElementById('edit-position').value = position;
        document.getElementById('edit-name').value = name;
        document.getElementById('edit-designation').value = designation;
        document.getElementById('edit-display-order').value = displayOrder;

        // Switch button states
        document.getElementById('add-btn').style.display = 'none';
        document.getElementById('update-btn').style.display = 'inline-block';
        document.getElementById('cancel-btn').style.display = 'inline-block';
    }

    function cancelEdit() {
        // Clear form
        document.getElementById('edit-id').value = '';
        document.getElementById('edit-position').value = '';
        document.getElementById('edit-name').value = '';
        document.getElementById('edit-designation').value = '';
        document.getElementById('edit-display-order').value = '';

        // Reset button states
        document.getElementById('add-btn').style.display = 'inline-block';
        document.getElementById('update-btn').style.display = 'none';
        document.getElementById('cancel-btn').style.display = 'none';
    }
    </script>
</body>
</html>