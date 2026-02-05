<?php
session_start();
include '../PHP/Config.php';

$doctor_id = $_SESSION['doctor_id'] ?? null;
if (!$doctor_id) {
    header("Location: ../HTML/Login.html");
    exit;
}

// --- DELETE SCHEDULE ---
if (isset($_GET['delete_id'])) {
    $schedule_id = intval($_GET['delete_id']);
    $sql = "DELETE FROM doctor_schedule WHERE `Schedule ID` = ? AND `Doctor ID` = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $schedule_id, $doctor_id);

    if ($stmt->execute()) {
        echo "success"; // ✅ return plain text
    } else {
        echo "error"; // ✅ return error
    }
    exit; // stop further output
}

// --- INSERT or UPDATE SCHEDULE ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $day = $_POST['day'] ?? '';
    $start_time = $_POST['start_time'] ?? '';
    $end_time = $_POST['end_time'] ?? '';
    $slot_duration = $_POST['slot_duration'] ?? '';
    $num_slots = $_POST['num_slots_hidden'] ?? '';

    // Validate inputs
    if (!$day || !$start_time || !$end_time || !$slot_duration || !$num_slots) {
        die("All fields are required.");
    }

    if (isset($_GET['id'])) {
        // UPDATE
        $schedule_id = intval($_GET['id']);
        $sql = "UPDATE doctor_schedule 
                SET `Day`=?, `Start Time`=?, `End Time`=?, `Slot Duration`=?, `Number of Slots`=? 
                WHERE `Schedule ID`=? AND `Doctor ID`=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssiiii", $day, $start_time, $end_time, $slot_duration, $num_slots, $schedule_id, $doctor_id);
        if($stmt->execute()){
            header("Location: ../HTML/DoctorDashboard.php?success=2"); // success=2 = edited
            exit;
        } else {
            die("Error updating schedule: " . $conn->error);
        }
    } else {
        // INSERT
        $sql =  "INSERT INTO doctor_schedule 
			(`Doctor ID`, `Day`, `Start Time`, `End Time`, `Slot Duration`, `Number of Slots`)
			VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
		$stmt->bind_param("isssii", $doctor_id, $day, $start_time, $end_time, $slot_duration, $num_slots);
        if($stmt->execute()){
            header("Location: ../HTML/DoctorDashboard.php?success=1");
			exit;
        } else {
            die("Error saving schedule: " . $conn->error);
        }
    }
}

// --- FETCH SCHEDULES ---
$schedule_sql = "SELECT `Schedule ID` AS ID, `Day`, `Start Time`, `End Time`, `Slot Duration`, `Number of Slots`
                 FROM doctor_schedule
                 WHERE `Doctor ID` = ?
                 ORDER BY FIELD(`Day`, 'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday')";
$stmt = $conn->prepare($schedule_sql);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();
$schedules = $result->fetch_all(MYSQLI_ASSOC);
?>
