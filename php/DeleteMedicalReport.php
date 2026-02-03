<?php
session_start();
include '../PHP/Config.php';

$doctor_id = $_SESSION['doctor_id'] ?? null;
if (!$doctor_id) {
    header('Location: ../HTML/Login.html');
    exit;
}

// Check required parameters
if (isset($_GET['id'], $_GET['nic'])) {
    $report_id = $_GET['id'];
    $nic = $_GET['nic'];

    // Delete the report
    $sql = "DELETE FROM medical_reports WHERE ReportID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $report_id);
    $stmt->execute();
    $stmt->close();
}

// Redirect back to the same patient's reports page
header("Location: ../HTML/ViewMedicalReports.php?nic=" . urlencode($nic));
exit;
?>
