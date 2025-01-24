<?php
require 'vendor/autoload.php'; // Make sure to use the correct path for autoload
use Dompdf\Dompdf;
use Dompdf\Options;
session_start();
@include 'db.php';

// Check if user is not logged in
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['update_signatories'])) {
    foreach ($_POST['signatories'] as $id => $data) {
        $name = $data['name'];
        $position = $data['position'];
        $query = "UPDATE signatories SET name = ?, position = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ssi", $name, $position, $id);
        mysqli_stmt_execute($stmt);
    }
    header("Location: generate_report.php");
    exit();
}

// Fetch signatories
$signatories_query = "SELECT * FROM signatories";
$signatories_result = mysqli_query($conn, $signatories_query);
$signatories = mysqli_fetch_all($signatories_result, MYSQLI_ASSOC);

// Check if form is submitted
if (isset($_POST['generate_report'])) {
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    // Initialize DOMPDF
    $options = new Options();
    $options->setChroot(__DIR__);
    $dompdf = new Dompdf($options);
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isRemoteEnabled', true);

    // Query for overall statistics
    $stats_query = "SELECT 
        COUNT(*) as total_items,
        SUM(CASE WHEN status = 'Unclaimed' AND remark = 'Approved' THEN 1 ELSE 0 END) as unclaimed_items,
        SUM(CASE WHEN status = 'Claimed' AND remark = 'Approved' THEN 1 ELSE 0 END) as claimed_items
    FROM reported_items 
    WHERE report_date BETWEEN ? AND ? AND remark = 'Approved'";

    $stmt = mysqli_prepare($conn, $stats_query);
    mysqli_stmt_bind_param($stmt, "ss", $start_date, $end_date);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $stats = mysqli_fetch_assoc($result);

    $total_items = $stats['total_items'];
    $unclaimed_percentage = $total_items > 0 ? ($stats['unclaimed_items'] / $total_items * 100) : 0;
    $claimed_percentage = $total_items > 0 ? ($stats['claimed_items'] / $total_items * 100) : 0;

    // Query for categories
    $categories_query = "SELECT 
        item_category,
        COUNT(*) as total_count,
        SUM(CASE WHEN status = 'Unclaimed' THEN 1 ELSE 0 END) as lost_count,
        SUM(CASE WHEN status = 'Claimed' THEN 1 ELSE 0 END) as claimed_count
    FROM reported_items
    WHERE report_date BETWEEN ? AND ? AND remark = 'Approved'
    GROUP BY item_category
    ORDER BY total_count DESC LIMIT 5";

    $stmt = mysqli_prepare($conn, $categories_query);
    mysqli_stmt_bind_param($stmt, "ss", $start_date, $end_date);
    mysqli_stmt_execute($stmt);
    $categories_result = mysqli_stmt_get_result($stmt);

    // Query for college statistics
    $college_query = "SELECT 
        dept_college,
        COUNT(*) as total_reports,
        SUM(CASE WHEN status = 'Unclaimed' THEN 1 ELSE 0 END) as lost_items,
        SUM(CASE WHEN status = 'Claimed' THEN 1 ELSE 0 END) as claimed_items
    FROM reported_items
    WHERE report_date BETWEEN ? AND ? AND remark = 'Approved'
    GROUP BY dept_college
    ORDER BY total_reports DESC";

    $stmt = mysqli_prepare($conn, $college_query);
    mysqli_stmt_bind_param($stmt, "ss", $start_date, $end_date);
    mysqli_stmt_execute($stmt);
    $college_result = mysqli_stmt_get_result($stmt);

    // HTML content with smaller fonts
    $html = '
    <style>
    body { font-family: Arial, sans-serif; font-size: 12px; }
    .header { 
        text-align: center; 
        display: inline-block; 
        width: 100%;
        margin-top: 20px;
        margin-bottom: 20px;
        position: relative; /* Make the header a positioned element */
    }
    .logo {
        vertical-align: top;
        width: 80px;
        margin-right: -70px;
        margin-top: -20px;
    }
    .text-content { 
        display: inline-block;
        vertical-align: middle;
        text-align: center;
    }
    .title { font-weight: bold; font-size: 16px; }
    .college { font-weight: bold; font-size: 12px; }
    .subtitle { font-weight: bold; font-size: 12px; }
    .reptitle { font-weight: bold; font-size: 14px; }
    .timestamp {
        position: absolute; 
        top: -40px;
        right: 0;
        font-size: 10px;
        text-align: right;
    }
        .table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 20px; 
            font-size: 11px; 
        }
        .table, .table th, .table td { 
            border: 1px solid black; 
        }
        .table th, .table td { 
            padding: 6px; 
            text-align: center; 
        }
        .signatories {
            text-align: center;
            margin-top: 40px;
        }
        .sign-row {
            display: block;
            margin-top: 30px;
        }
        .sign-col {
            width: 48%; /* Each column takes up almost half of the page width */
            float: left;
            font-size: 12px;
        }
        .sign-col strong {
            display: block;
            margin-bottom: 5px;
        }
        .sign-col small {
            display: block;
            font-size: 12px;
        }
        /* Clear floats to prevent layout issues */
        .sign-row:after {
            content: "";
            display: table;
            clear: both;
        }
</style>
    
    <div class="header">
        <!-- University Logo -->
        <img class="logo" src="images/cvsu_logo.png" alt="University Logo">
        
        <!-- Text Content -->
        <div class="text-content">
            <div>Republic of the Philippines</div>
            <div class="title">CAVITE STATE UNIVERSITY</div>
            <div class="subtitle">Don Severino delas Alas Campus</div>
            <div>Indang, Cavite</div>
            <br>
            <div class="college">COLLEGE OF ENGINEERING AND INFORMATION TECHNOLOGY</div>
            <br>
            <div class="reptitle">LOST AND FOUND REPORT</div>
            <div>Report Period: ' . date('F d, Y', strtotime($start_date)) . ' to ' . date('F d, Y', strtotime($end_date)) . '</div>
        </div>
        <div class="timestamp">
            Generated on: ' . date('F d, Y') . '
        </div>
    </div>';
    

    $html .= '<h3>1. Overall Statistics</h3>
    <p><strong>Total Reports:</strong> ' . $total_items . '<br>
    <strong>Unclaimed Items:</strong> ' . $stats['unclaimed_items'] . ' (' . number_format($unclaimed_percentage, 1) . '%)<br>
    <strong>Claimed Items:</strong> ' . $stats['claimed_items'] . ' (' . number_format($claimed_percentage, 1) . '%)</p>';

    $html .= '<h3>2. Most Frequent Item Categories</h3>';
    $html .= '<table class="table">
                <tr>
                    <th>Category</th>
                    <th>Total Items</th>
                    <th>Unclaimed</th>
                    <th>Claimed</th>
                </tr>';
    while ($row = mysqli_fetch_assoc($categories_result)) {
        $html .= '<tr>
                    <td>' . $row['item_category'] . '</td>
                    <td>' . $row['total_count'] . '</td>
                    <td>' . $row['lost_count'] . '</td>
                    <td>' . $row['claimed_count'] . '</td>
                  </tr>';
    }
    $html .= '</table>';

    $html .= '<h3>3. Reports by College</h3>';
    $html .= '<table class="table">
                <tr>
                    <th>College</th>
                    <th>Total Reports</th>
                    <th>Lost Items</th>
                    <th>Claimed Items</th>
                </tr>';
    while ($row = mysqli_fetch_assoc($college_result)) {
        $html .= '<tr>
                    <td>' . $row['dept_college'] . '</td>
                    <td>' . $row['total_reports'] . '</td>
                    <td>' . $row['lost_items'] . '</td>
                    <td>' . $row['claimed_items'] . '</td>
                  </tr>';
    }
    $html .= '</table>
    <div class="signatories">';
    $counter = 0; // Counter to track column positions
    
    foreach ($signatories as $signatory) {
        // Start a new row if the counter is 0
        if ($counter % 2 === 0) {
            $html .= '<div class="sign-row">';
        }
    
        // Add a signatory column
        $html .= '<div class="sign-col">
            <strong>' . htmlspecialchars($signatory['role']) . ':</strong> ' . htmlspecialchars($signatory['name']) . '<br>';
        if (!empty($signatory['position'])) {
            $html .= '<small>' . htmlspecialchars($signatory['position']) . '</small>';
        }
        $html .= '</div>';
    
        $counter++;
    
        // Close the row after two columns
        if ($counter % 2 === 0) {
            $html .= '</div>';
        }
    }
    
    // Close the last row if it’s incomplete
    if ($counter % 2 !== 0) {
        $html .= '</div>';
    }
    
    $html .= '</div>';
    

    // Load content into DOMPDF
    $dompdf->loadHtml($html);

    // Setup the paper size and orientation
    $dompdf->setPaper('A4', 'portrait');

    // Render the PDF
    $dompdf->render();

    // Output the generated PDF to Browser
    $dompdf->stream('Lost&Found_report_' . date('Y-m-d') . '.pdf', ['Attachment' => false]);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Report</title>
    <link rel="icon" href="images/kitaoldlogo.png" type="img/png" sizes="48x48">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="fontawesome/css/all.min.css">
    <script src="js/bootstrap.bundle.min.js"></script>
    <style>
        .form-container {
            background-color: #E1E8ED; /* Light gray color */
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .form-container h4 {
            margin-top: 20px;
            margin-bottom: 20px;
        }
        .form-container .form-group {
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <?php include "sidebar.php"; ?>
    
    <div class="content container-fluid">
        <div class="dashboard-header">
            <div class="d-flex justify-content-between align-items-center">
                <h2>Lost and Found Report</h2>
            </div>
        </div>
        
        <div class="form-container">
            <!-- Report Generation Form -->
            <form method="POST" class="mt-6">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="start_date">Start Date:</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="end_date">End Date:</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <button type="submit" name="generate_report" class="btn btn-success form-control">
                                <i class="fas fa-file-pdf me-2"></i>Generate Report
                            </button>
                        </div>
                    </div>
                </div>
            </form>

            <h4>Update Signatories</h4>

            <!-- Signatories Update Form -->
            <form method="POST">
                <div class="row">
                    <?php foreach ($signatories as $signatory): ?>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><?= $signatory['role'] ?>:</label>
                            <input placeholder="Enter Name" type="text" name="signatories[<?= $signatory['id'] ?>][name]" 
                                    value="<?= htmlspecialchars($signatory['name']) ?>" class="form-control" required>
                            <input placeholder="Enter Designation" type="text" name="signatories[<?= $signatory['id'] ?>][position]" 
                                    value="<?= htmlspecialchars($signatory['position']) ?>" class="form-control mt-2">
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <button type="submit" name="update_signatories" class="btn btn-success mt-3">Update Signatories</button>
            </form>
        </div>
    </div>
</body>
</html>