<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../PHP/Config.php'; // if CancelAppointment.php is already in PHP folder

session_start();
header('Content-Type: application/json; charset=utf-8');

// Prevent PHP warnings from being displayed to the client (they'll be logged)
ini_set('display_errors', '0');
ini_set('log_errors', '1');
// Optional: set your own error log file
// ini_set('error_log', __DIR__ . '/php-error.log');


$response = ['success' => false, 'message' => 'Unknown error'];

try {
    $patient_id = $_SESSION['patient_id'] ?? null;
    if (!$patient_id) {
        $response['message'] = 'Not logged in';
        echo json_encode($response);
        exit;
    }

    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        $response['message'] = 'Invalid appointment id';
        echo json_encode($response);
        exit;
    }

    $appointment_id = (int) $_GET['id'];
    $patient_id = (int) $patient_id;

    // Update status
    $sql = "UPDATE appointments SET Status = 'CANCELLED' WHERE AppointmentID = ? AND PatientID = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ii", $appointment_id, $patient_id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $response = ['success' => true, 'message' => 'Appointment cancelled successfully'];
            $stmt->close();
            echo json_encode($response);
            $conn->close();
            exit;
        } else {
            // No rows affected — explain why
            $stmt->close();
            $checkSql = "SELECT Status FROM appointments WHERE AppointmentID = ? AND PatientID = ?";
            if ($s2 = $conn->prepare($checkSql)) {
                $s2->bind_param("ii", $appointment_id, $patient_id);
                $s2->execute();
                $res = $s2->get_result();
                if ($res && $res->num_rows > 0) {
                    $row = $res->fetch_assoc();
                    $response['message'] = 'Unable to cancel. Current status: ' . ($row['Status'] ?? 'UNKNOWN');
                } else {
                    $response['message'] = 'Appointment not found for this patient';
                }
                $s2->close();
            } else {
                error_log("Prepare failed (check): " . $conn->error);
                $response['message'] = 'Database error';
            }
            echo json_encode($response);
            $conn->close();
            exit;
        }
    } else {
        error_log("Prepare failed: " . $conn->error);
        $response['message'] = 'Database error';
        echo json_encode($response);
        $conn->close();
        exit;
    }

} catch (Exception $e) {
    error_log("CancelAppointment exception: " . $e->getMessage());
    $response['message'] = 'Server error';
    echo json_encode($response);
    if (isset($conn)) $conn->close();
    exit;
}
