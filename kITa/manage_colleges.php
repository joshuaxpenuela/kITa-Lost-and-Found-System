<?php
include 'db.php';

// Handle POST requests for adding/updating colleges
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = array();
    
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                if (isset($_POST['college_name']) && !empty($_POST['college_name'])) {
                    $collegeName = trim($_POST['college_name']);
                    
                    // Validate college name - only letters and spaces allowed
                    if (!preg_match("/^[a-zA-Z\s]+$/", $collegeName)) {
                        $response = array(
                            'status' => 'error',
                            'message' => 'College name can only contain letters and spaces'
                        );
                        break;
                    }
                    
                    // Check if college already exists
                    $checkSql = "SELECT COUNT(*) as count FROM colleges WHERE college = ?";
                    $checkStmt = $conn->prepare($checkSql);
                    $checkStmt->bind_param("s", $collegeName);
                    $checkStmt->execute();
                    $result = $checkStmt->get_result();
                    $row = $result->fetch_assoc();
                    
                    if ($row['count'] > 0) {
                        $response = array(
                            'status' => 'error',
                            'message' => 'College already exists'
                        );
                    } else {
                        // Insert new college
                        $sql = "INSERT INTO colleges (college, status) VALUES (?, 'enabled')";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("s", $collegeName);
                        
                        if ($stmt->execute()) {
                            $response = array(
                                'status' => 'success',
                                'message' => 'College added successfully'
                            );
                        } else {
                            $response = array(
                                'status' => 'error',
                                'message' => 'Failed to add college'
                            );
                        }
                        $stmt->close();
                    }
                    $checkStmt->close();
                } else {
                    $response = array(
                        'status' => 'error',
                        'message' => 'College name cannot be empty'
                    );
                }
                break;
                
            case 'toggle_status':
                if (isset($_POST['college_id']) && !empty($_POST['college_id'])) {
                    $collegeId = $_POST['college_id'];
                    $newStatus = $_POST['status'] === 'enabled' ? 'disabled' : 'enabled';
                    
                    // Update college status
                    $sql = "UPDATE colleges SET status = ? WHERE college_id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("si", $newStatus, $collegeId);
                    
                    if ($stmt->execute()) {
                        $response = array(
                            'status' => 'success',
                            'message' => 'College status updated successfully'
                        );
                    } else {
                        $response = array(
                            'status' => 'error',
                            'message' => 'Failed to update college status'
                        );
                    }
                    $stmt->close();
                } else {
                    $response = array(
                        'status' => 'error',
                        'message' => 'Invalid college ID'
                    );
                }
                break;
                
            default:
                $response = array(
                    'status' => 'error',
                    'message' => 'Invalid action'
                );
        }
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Handle GET requests to fetch colleges
$sql = "SELECT * FROM colleges ORDER BY college";
$result = $conn->query($sql);

$colleges = array();
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $colleges[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode($colleges);
?>