<?php
session_start(); // MUST be at the top
ini_set('display_errors', 1);
error_reporting(E_ALL);

include '../PHP/Config.php';

$doctor_id = $_SESSION['doctor_id'] ?? null;
if (!$doctor_id) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patient_id = $_POST['patient_id'];
    $doctor_id = $_POST['doctor_id'];
    $test_type = $_POST['test_type'];
    $test_date = $_POST['test_date'];
    $priority = $_POST['priority'];
    $notes = $_POST['notes'] ?? '';

    // Handle file upload
    $attachment_path = null;
    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/medical_reports/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        $filename = time() . '_' . basename($_FILES['attachment']['name']);
        $targetFile = $uploadDir . $filename;
        if (move_uploaded_file($_FILES['attachment']['tmp_name'], $targetFile)) {
            $attachment_path = $filename;
        }
    }

    $sql = "INSERT INTO medical_reports 
            (PatientID, DoctorID, TestType, TestDate, Priority, Notes, Attachments)
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iisssss", $patient_id, $doctor_id, $test_type, $test_date, $priority, $notes, $attachment_path);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => $conn->error]);
    }
}
