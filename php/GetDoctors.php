<?php
include '../PHP/Config.php';

$facility_id = $_GET['facility_id'] ?? null;
if (!$facility_id) {
    echo json_encode([]);
    exit;
}

// Compare as string, because doctors.`Facility Name` stores ID as string
$stmt = $conn->prepare("
    SELECT `Doctor ID` AS DoctorID, `First Name` AS FirstName, `Last Name` AS LastName, `Specialization`
    FROM doctors
    WHERE TRIM(`Facility Name`) = ?
");
$stmt->bind_param("s", $facility_id);
$stmt->execute();
$result = $stmt->get_result();

$doctors = [];
while ($row = $result->fetch_assoc()) {
    $doctors[] = $row;
}

header('Content-Type: application/json');
echo json_encode($doctors);
