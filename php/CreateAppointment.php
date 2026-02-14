<?php
session_start();
include '../PHP/Config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patient_id = $_POST['patient_id'];
    $doctor_id = $_POST['doctor_id'];
    $facility_id = $_POST['facility_id'];
    $date = $_POST['date'];
    $time_slot = $_POST['time_slot'];

    // Basic validation
    if (!$patient_id || !$doctor_id || !$facility_id || !$date || !$time_slot) {
        die("All fields are required.");
    }

    // Check if slot is already booked
    $check_sql = "SELECT * FROM appointments 
                  WHERE DoctorID=? AND Date=? AND Time=? AND Status='BOOKED'";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("iss", $doctor_id, $date, $time_slot);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        die("This time slot is already booked.");
    }

    // Insert appointment
    $insert_sql = "INSERT INTO appointments (PatientID, DoctorID, FacilityID, Date, Time) 
                   VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insert_sql);
    $stmt->bind_param("iiiss", $patient_id, $doctor_id, $facility_id, $date, $time_slot);

    if ($stmt->execute()) {
        header("Location: ../HTML/PatientDashboard.php?success=1");
        exit;
    } else {
        echo "Error booking appointment: " . $conn->error;
    }
}
?>
