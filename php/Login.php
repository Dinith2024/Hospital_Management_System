<?php
session_start();
include 'Config.php';

$response = ['success' => false, 'message' => '', 'redirect' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_type = $_POST['user_type'];
    $id = trim($_POST['nic']);
    $password = $_POST['password'];

    if ($user_type === 'patient') {
		$table = 'patients';
		$id_column = 'NIC';
		$password_column = 'Password';
		$redirect = '../HTML/PatientDashboard.php'; // <-- optional: patient dashboard
	} elseif ($user_type === 'doctor') {
		$table = 'doctors';
		$id_column = 'License Number';
		$password_column = 'Password';
		$redirect = '../HTML/DoctorDashboard.php'; // <-- redirect to doctor dashboard
	} else {
        $response['message'] = 'Invalid user type.';
        echo json_encode($response);
        exit;
    }

    $stmt = $conn->prepare("SELECT * FROM `$table` WHERE `$id_column` = ?");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        $hashedPassword = trim($user[$password_column]);

        if (password_verify($password, $hashedPassword)) {
            $_SESSION['user_type'] = $user_type;
			if ($user_type === 'doctor') {
				// Store doctor_id in session for dashboard use
				$_SESSION['doctor_id'] = $user['Doctor ID']; // <-- make sure this column exists
			} else {
				// Store patient identifier
				$_SESSION['patient_id'] = $user['PatientID'];
			}

            $response['success'] = true;
            $response['message'] = 'Login successful!';
            $response['redirect'] = $redirect; // send redirect URL
        } else {
            $response['message'] = 'Incorrect password.';
        }
    } else {
        $response['message'] = ucfirst($user_type) . ' not found.';
    }

	error_log(print_r($response, true));

    echo json_encode($response);
}
?>
