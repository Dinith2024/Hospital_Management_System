<?php
session_start();
include '../PHP/Config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patient_id = $_POST['patient_id'] ?? null;
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $dob = $_POST['dob'] ?? null;
    $blood_type = trim($_POST['blood_type'] ?? '');
    $allergies = trim($_POST['allergies'] ?? '');
    $medical_history = trim($_POST['medical_history'] ?? '');

    if (!$patient_id || !$email || !$phone) {
        die("Patient ID, email, and phone are required.");
    }

    $sql = "UPDATE patients SET 
                `Email Address` = ?, 
                `Phone Number` = ?, 
                `Date of Birth` = ?, 
                `Blood Type` = ?, 
                `Allergies` = ?, 
                `Medical History` = ? 
            WHERE `PatientID` = ?";

    $stmt = $conn->prepare($sql);
    if (!$stmt) die("Prepare failed: ".$conn->error);

    $stmt->bind_param("ssssssi", $email, $phone, $dob, $blood_type, $allergies, $medical_history, $patient_id);

    if ($stmt->execute()) {
        header("Location: ../HTML/PatientDashboard.php?success=1");
        exit;
    } else {
        die("Error updating profile: ".$stmt->error);
    }
}

?>
