<?php
session_start();
include '../PHP/Config.php';

$patient_id = $_SESSION['patient_id'] ?? null;
if(!$patient_id){
    echo json_encode([]);
    exit;
}

$sql = "SELECT a.AppointmentID, a.Date AS AppointmentDate, a.Time AS AppointmentTime, 
        d.`First Name` as DoctorFirstName, d.`Last Name` as DoctorLastName, 
        d.Specialization, f.`Facility Name` AS FacilityName, a.Status
        FROM appointments a
        JOIN doctors d ON a.DoctorID = d.`Doctor ID`
        JOIN facility f ON a.FacilityID = f.`Facility ID`
        WHERE a.PatientID = ?
        ORDER BY a.Date DESC, a.Time DESC";
		
$stmt = $conn->prepare($sql);
if(!$stmt){
    echo json_encode(["error" => $conn->error]);
    exit;
}

$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();

if(!$result){
    echo json_encode(["error" => $conn->error]);
    exit;
}

$appointments = $result->fetch_all(MYSQLI_ASSOC);
echo json_encode($appointments);
?>
