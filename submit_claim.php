<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "lost_found_db";
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => "Connection failed: " . $conn->connect_error]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Log raw POST data for debugging
    error_log("Raw POST data: " . print_r($_POST, true));

    // Validate input data
    $required_fields = [
        'id_item', 'validID', 'ProofOwner', 'claim_desc', 
        'claim_Fname', 'claim_Lname', 'claim_email', 
        'claim_contact', 'claim_dept'
    ];

    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])) {
            http_response_code(400);
            echo json_encode(['error' => "Missing required field: $field"]);
            exit();
        }
    }

    // Retrieve POST data
    $id_item = intval($_POST['id_item']);
    $validID = $_POST['validID'];
    $ProofOwner = $_POST['ProofOwner'];
    $claim_desc = $_POST['claim_desc'];
    $claim_Fname = $_POST['claim_Fname'];
    $claim_Lname = $_POST['claim_Lname'];
    $claim_email = $_POST['claim_email'];
    $claim_contact = $_POST['claim_contact'];
    $claim_dept = $_POST['claim_dept'];

    // Validate file uploads
    $validID_data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $validID));
    $proofOwner_data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $ProofOwner));

    if (!$validID_data || !$proofOwner_data) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid image data']);
        exit();
    }

    // Generate unique filenames for both images
    $validID_name = uniqid() . '_validID.jpg';
    $proofOwner_name = uniqid() . '_proofOwner.jpg';
    
    // Ensure uploads directory exists
    $upload_dir = 'uploads/img_valid_ids/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Upload paths for both images
    $validID_upload_path = $upload_dir . $validID_name;
    $proofOwner_upload_path = $upload_dir . $proofOwner_name;

    // Save uploaded images
    if (!file_put_contents($validID_upload_path, $validID_data) || 
        !file_put_contents($proofOwner_upload_path, $proofOwner_data)) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to save uploaded images']);
        exit();
    }

    // Get current date and time
    $claim_date = date('Y-m-d');
    $claim_time = date('H:i:s');

    // Fetch the details of the reported item
    $fetch_item_query = "SELECT * FROM reported_items WHERE id_item = ?";
    $fetch_stmt = $conn->prepare($fetch_item_query);
    
    if (!$fetch_stmt) {
        http_response_code(500);
        echo json_encode(['error' => "Prepare failed: " . $conn->error]);
        exit();
    }
    
    $fetch_stmt->bind_param("i", $id_item);
    $fetch_stmt->execute();
    $result = $fetch_stmt->get_result();
    $item_details = $result->fetch_assoc();
    $fetch_stmt->close();

    if (!$item_details) {
        http_response_code(404);
        echo json_encode(['error' => "No item found with the given ID"]);
        exit();
    }

    // Prepare insert for claim_reports table
    $claim_reports_query = "INSERT INTO claim_reports (
        id_item, Fname, Lname, email, contact_no, dept_college, 
        item_name, item_category, location_found, report_date, report_time, other_details, 
        img1, img2, img3, img4, img5, status, remark, 
        validID, ProofOwner, claim_desc, 
        claim_Fname, claim_Lname, claim_email, 
        claim_contact, claim_dept, claim_date, claim_time
    ) VALUES (
        ?, ?, ?, ?, ?, ?, 
        ?, ?, ?, ?, ?, ?,
        ?, ?, ?, ?, ?, ?, ?, 
        ?, ?, ?, 
        ?, ?, ?, 
        ?, ?, ?, ?
    )";

    $claim_reports_stmt = $conn->prepare($claim_reports_query);
    
    if (!$claim_reports_stmt) {
        http_response_code(500);
        echo json_encode(['error' => "Prepare failed: " . $conn->error]);
        exit();
    }

    // Explicitly set default status and remark for claim_reports
    $default_status = 'Unclaimed';
    $default_remark = 'Pending';

    $claim_reports_stmt->bind_param(
        "issssssssssssssssssssssssssss",
        $item_details['id_item'],
        $item_details['Fname'],
        $item_details['Lname'],
        $item_details['email'],
        $item_details['contact_no'],
        $item_details['dept_college'],
        $item_details['item_name'],
        $item_details['item_category'],
        $item_details['location_found'],
        $item_details['report_date'],
        $item_details['report_time'],
        $item_details['other_details'],
        $item_details['img1'],
        $item_details['img2'],
        $item_details['img3'],
        $item_details['img4'],
        $item_details['img5'],
        $default_status,
        $default_remark,
        $validID_name,
        $proofOwner_name,
        $claim_desc,
        $claim_Fname,
        $claim_Lname,
        $claim_email,
        $claim_contact,
        $claim_dept,
        $claim_date,
        $claim_time
    );

    // Update the reported_items table
    $update_query = "UPDATE reported_items SET 
        status = 'Unclaimed',
        remark = 'Pending'
        WHERE id_item = ?";
    
    $update_stmt = $conn->prepare($update_query);
    
    if (!$update_stmt) {
        http_response_code(500);
        echo json_encode(['error' => "Prepare failed: " . $conn->error]);
        exit();
    }
    
    $update_stmt->bind_param("i", $id_item);

    // Start a transaction
    $conn->begin_transaction();

    try {
        // Execute both statements
        $update_result = $update_stmt->execute();
        
        if ($update_result === false) {
            throw new Exception("Update failed: " . $update_stmt->error);
        }
        
        $claim_reports_result = $claim_reports_stmt->execute();
        
        if ($claim_reports_result === false) {
            throw new Exception("Claim reports insert failed: " . $claim_reports_stmt->error);
        }

        $conn->commit();
        echo json_encode(['message' => "Claim submitted successfully"]);
    } catch (Exception $e) {
        $conn->rollback();
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }

    // Close statements
    $update_stmt->close();
    $claim_reports_stmt->close();
    $conn->close();
} else {
    http_response_code(405);
    echo json_encode(['error' => "Invalid request method"]);
}
?>