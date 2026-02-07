<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
include '../PHP/Config.php';

// Ensure request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$report_id = isset($_POST['report_id']) ? (int)$_POST['report_id'] : null;
$test_date = $_POST['test_date'] ?? null;
$priority  = $_POST['priority'] ?? null;
$notes     = $_POST['notes'] ?? null;

if (!$report_id || !$test_date || !$priority) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

// Handle file upload if provided
$attachment_name = null;
if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
    $upload_dir = '../uploads/medical_reports/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $attachment_name = time() . "_" . basename($_FILES['attachment']['name']);
    $target_path = $upload_dir . $attachment_name;

    if (!move_uploaded_file($_FILES['attachment']['tmp_name'], $target_path)) {
        echo json_encode(['success' => false, 'message' => 'File upload failed']);
        exit;
    }
}

// Build SQL
if ($attachment_name) {
    $sql = "UPDATE medical_reports SET TestDate=?, Priority=?, Notes=?, Attachments=? WHERE ReportID=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $test_date, $priority, $notes, $attachment_name, $report_id);
} else {
    $sql = "UPDATE medical_reports SET TestDate=?, Priority=?, Notes=? WHERE ReportID=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $test_date, $priority, $notes, $report_id);
}

$stmt->execute();
if ($stmt->affected_rows > 0) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Update failed. Maybe no changes or invalid ReportID. Error: ' . $stmt->error
    ]);
}

$stmt->close();
$conn->close();
