<?php
session_start();
@include 'db.php';

// Redirect if not an admin
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Database connection error handling
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Function to log admin actions
function logAdminAction($conn, $action, $details) {
    $admin_id = $_SESSION['admin_id'] ?? 0;
    $log_query = "INSERT INTO admin_logs (admin_id, action, details, log_date) VALUES 
                  ('$admin_id', '" . mysqli_real_escape_string($conn, $action) . "', 
                   '" . mysqli_real_escape_string($conn, $details) . "', NOW())";
    mysqli_query($conn, $log_query);
}

// Handling claim request actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['approve_claim']) || isset($_POST['deny_claim'])) {
        // Sanitize input
        $id_claim = mysqli_real_escape_string($conn, $_POST['id_claim']);
        $id_item = mysqli_real_escape_string($conn, $_POST['id_item']);

        if (isset($_POST['approve_claim'])) {
            // Begin a transaction to ensure data integrity
            mysqli_begin_transaction($conn);

            try {
                // 1. Fetch full claim details
                $claim_query = "SELECT * FROM claim_reports WHERE id_claim = '$id_claim'";
                $claim_result = mysqli_query($conn, $claim_query);
                $claim_data = mysqli_fetch_assoc($claim_result);

                // 2. Update the specific approved claim
                $update_approved_claim = "UPDATE claim_reports 
                                          SET remark = 'Approved', 
                                              status = 'Claimed' 
                                          WHERE id_claim = '$id_claim'";
                mysqli_query($conn, $update_approved_claim);

                $update_reported_item = "UPDATE reported_items 
                                        SET remark = 'Approved', 
                                            status = 'Claimed' 
                                        WHERE id_item = '$id_item'";
                mysqli_query($conn, $update_reported_item);

                // 4. Insert into claimed_items table
                $insert_claimed_item = "INSERT INTO claim_reports (
                    id_item, 
                    item_name, 
                    item_category, 
                    location_found, 
                    claim_Fname, 
                    claim_lname, 
                    claim_email, 
                    claim_contact, 
                    claim_dept, 
                    claim_date,
                    validID,
                    ProofOwner,
                    other_details
                ) VALUES (
                    '$id_item', 
                    '" . mysqli_real_escape_string($conn, $claim_data['item_name']) . "', 
                    '" . mysqli_real_escape_string($conn, $claim_data['item_category']) . "', 
                    '" . mysqli_real_escape_string($conn, $claim_data['location_found']) . "', 
                    '" . mysqli_real_escape_string($conn, $claim_data['claim_Fname']) . "', 
                    '" . mysqli_real_escape_string($conn, $claim_data['claim_Lname']) . "', 
                    '" . mysqli_real_escape_string($conn, $claim_data['claim_email']) . "', 
                    '" . mysqli_real_escape_string($conn, $claim_data['claim_contact']) . "', 
                    '" . mysqli_real_escape_string($conn, $claim_data['claim_dept']) . "', 
                    NOW(),
                    '" . mysqli_real_escape_string($conn, $claim_data['validID']) . "',
                    '" . mysqli_real_escape_string($conn, $claim_data['ProofOwner']) . "',
                    '" . mysqli_real_escape_string($conn, $claim_data['claim_desc']) . "'
                )";
                mysqli_query($conn, $insert_claimed_item);

                // 5. Deny all other pending claims for this item
                $update_other_claims = "UPDATE claim_reports 
                                        SET remark = 'Denied', 
                                            status = 'Unclaimed' 
                                        WHERE id_item = '$id_item' 
                                        AND id_claim != '$id_claim'
                                        AND remark = 'Pending'";
                mysqli_query($conn, $update_other_claims);

                // 6. Log the admin action
                logAdminAction($conn, 'Claim Approval', "Approved claim for item ID $id_item");

                // Commit the transaction
                mysqli_commit($conn);

                $_SESSION['message'] = "Claim request approved successfully! Other requests for this item have been denied.";
                $_SESSION['message_type'] = 'success';
            } catch (Exception $e) {
                // Rollback the transaction in case of any error
                mysqli_rollback($conn);
                
                // Log the error
                logAdminAction($conn, 'Claim Approval Failed', "Error: " . $e->getMessage());

                $_SESSION['message'] = "Error processing claim request: " . $e->getMessage();
                $_SESSION['message_type'] = 'danger';
            }
        } else {
            // Deny claim logic
            $update_query = "UPDATE claim_reports 
                             SET remark = 'Denied', 
                                 status = 'Unclaimed'
                             WHERE id_claim = '$id_claim'";
            $result = mysqli_query($conn, $update_query);

            if (!$result) {
                $_SESSION['message'] = "Error updating claim request: " . mysqli_error($conn);
                $_SESSION['message_type'] = 'danger';
                
                // Log the error
                logAdminAction($conn, 'Claim Denial Failed', "Error: " . mysqli_error($conn));
            } else {
                // Log the denied claim
                logAdminAction($conn, 'Claim Denial', "Denied claim for item ID $id_item");

                $_SESSION['message'] = "Claim request denied!";
                $_SESSION['message_type'] = 'warning';
            }
        }

        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Fetch unique items with pending claim requests
$items_query = "SELECT DISTINCT ri.id_item, ri.item_name, ri.location_found, ri.other_details, 
                       ri.img1, COUNT(cr.id_claim) as claim_count
                FROM reported_items ri
                JOIN claim_reports cr ON ri.id_item = cr.id_item
                WHERE cr.remark = 'Pending'
                GROUP BY ri.id_item
                ORDER BY claim_count DESC";
$items_result = mysqli_query($conn, $items_query);
if (!$items_result) {
    die("Error fetching items: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Item Claim Requests</title>
    <link rel="icon" href="images/kitaoldlogo.png" type="img/png">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/claim_style.css">
    <link rel="stylesheet" href="fontawesome/css/all.min.css">
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="claim_req.js"></script>
</head>
<body>
    <?php include "sidebar.php"; ?>
    <div class="content">
        <div class="container-fluid">
            <h1 class="mb-4">Claim Requests by Item</h1>

            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-<?php echo $_SESSION['message_type'] ?? 'info'; ?> alert-dismissible fade show" role="alert">
                    <?php 
                    echo $_SESSION['message']; 
                    unset($_SESSION['message']);
                    unset($_SESSION['message_type']); 
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php while ($item = mysqli_fetch_assoc($items_result)): ?>
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <?php echo htmlspecialchars($item['item_name']); ?> 
                            <small class="text-muted">(<?php echo $item['claim_count']; ?> claim request(s))</small>
                        </h5>
                        <div>
                            <strong>Location Found:</strong> 
                            <?php echo htmlspecialchars($item['location_found']); ?>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Item Image -->
                            <div class="col-md-3">
                                <?php if (!empty($item['img1'])): ?>
                                    <img src="../uploads/img_reported_items/<?php echo htmlspecialchars($item['img1']); ?>" 
                                         alt="Item Image" 
                                         class="item-thumbnail img-fluid mb-3"
                                         data-bs-toggle="modal"
                                         data-bs-target="#imageModal<?php echo $item['id_item']; ?>">
                                <?php else: ?>
                                    <div class="text-center mb-3">No Image</div>
                                <?php endif; ?>

                                <p><strong>Additional Details:</strong> 
                                    <?php echo htmlspecialchars($item['other_details']); ?>
                                </p>
                            </div>
                            
                            <!-- Claim Requests for this Item -->
                            <div class="col-md-9">
                                <div class="table-scroll-wrapper">
                                    <div class="table-scroll-header">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Claimer Name</th>
                                                    <th>Claim Description</th>
                                                    <th>Proof of Ownership</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                    <div class="table-scroll-body">
                                        <table class="table table-hover">
                                            <tbody>
                                                <?php 
                                                $item_id = $item['id_item'];
                                                $claims_query = "SELECT * FROM claim_reports 
                                                                WHERE id_item = '$item_id' AND remark = 'Pending'
                                                                ORDER BY claim_date DESC";
                                                $claims_result = mysqli_query($conn, $claims_query);
                                                
                                                while ($claim = mysqli_fetch_assoc($claims_result)):
                                                ?>
                                                    <tr>
                                                        <td>
                                                            <strong><?php echo htmlspecialchars($claim['claim_Fname'] . ' ' . $claim['claim_Lname']); ?></strong>
                                                            <br>
                                                            <small class="text-muted">
                                                                <?php echo htmlspecialchars($claim['claim_dept']); ?>
                                                            </small>
                                                        </td>
                                                        <td><?php echo htmlspecialchars($claim['claim_desc']); ?></td>
                                                        <td class="text-center">
                                                            <?php if (!empty($claim['validID'])): ?>
                                                                <div class="image-container">
                                                                    <img src="../uploads/img_valid_ids/<?php echo htmlspecialchars($claim['validID']); ?>" 
                                                                        alt="Valid ID" 
                                                                        class="proof-image"
                                                                        data-title="Valid ID - <?php echo htmlspecialchars($claim['claim_Fname'] . ' ' . $claim['claim_Lname']); ?>">
                                                                    <span class="image-label">Valid ID</span>
                                                                </div>
                                                            <?php endif; ?>
                                                            <?php if (!empty($claim['ProofOwner'])): ?>
                                                                <div class="image-container">
                                                                    <img src="../uploads/img_valid_ids/<?php echo htmlspecialchars($claim['ProofOwner']); ?>" 
                                                                        alt="Proof of Ownership" 
                                                                        class="proof-image"
                                                                        data-title="Proof of Ownership - <?php echo htmlspecialchars($claim['claim_Fname'] . ' ' . $claim['claim_Lname']); ?>">
                                                                    <span class="image-label">Proof of Ownership</span>
                                                                </div>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <form method="post" onsubmit="return confirm('Are you sure you want to process this claim?');">
                                                                <input type="hidden" name="id_claim" value="<?php echo $claim['id_claim']; ?>">
                                                                <input type="hidden" name="id_item" value="<?php echo $claim['id_item']; ?>">
                                                                <button type="submit" name="approve_claim" class="btn btn-success btn-sm w-100 mb-2">
                                                                    <i class="fas fa-check"></i> Approve
                                                                </button>
                                                                <button type="submit" name="deny_claim" class="btn btn-danger btn-sm w-100">
                                                                    <i class="fas fa-times"></i> Deny
                                                                </button>
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
                </div>


                <!-- Modal for Item Image -->
                <div class="modal fade" id="imageModal<?php echo $item['id_item']; ?>" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="imageModalLabel">Item Image</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <img src="../uploads/img_reported_items/<?php echo htmlspecialchars($item['img1']); ?>" alt="Item Image" class="img-fluid">
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>

            <?php if (mysqli_num_rows($items_result) == 0): ?>
                <div class="alert alert-info text-center">
                    No pending claim requests found.
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
