<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include '../PHP/Config.php';

$doctor_id = $_GET['doctor_id'] ?? null;
$date = $_GET['date'] ?? null; // YYYY-MM-DD from date picker

if (!$doctor_id || !$date) {
    echo json_encode([]);
    exit;
}

// Get day of the week
$dayOfWeek = date('l', strtotime($date)); // Monday, Tuesday, etc.

// Fetch all slots for that doctor on that day
$stmt = $conn->prepare("
    SELECT `StartTime`, `EndTime`
    FROM doctor_slots
    WHERE `DoctorID` = ? AND `Day` = ?
");
$stmt->bind_param("is", $doctor_id, $dayOfWeek);
$stmt->execute();
$result = $stmt->get_result();

$slots = [];
while ($row = $result->fetch_assoc()) {
    $slots[] = [
        'time' => $row['StartTime'] . '-' . $row['EndTime'],
        'status' => 'AVAILABLE'
    ];
}

// Fetch already booked appointments for that doctor on that date
$appt_stmt = $conn->prepare("
    SELECT `Time` 
    FROM appointments 
    WHERE DoctorID=? AND Date=? AND Status='BOOKED'
");
$appt_stmt->bind_param("is", $doctor_id, $date);
$appt_stmt->execute();
$booked_result = $appt_stmt->get_result();

$booked_slots = [];
while ($b = $booked_result->fetch_assoc()) {
    $booked_slots[] = $b['Time'];
}

// Mark booked slots as unavailable
foreach ($slots as &$slot) {
    if (in_array($slot['time'], $booked_slots)) {
        $slot['status'] = 'UNAVAILABLE';
    }
}

header('Content-Type: application/json');
echo json_encode($slots);
exit;
?>
