<?php
session_start();
require '../vendor/phpmailer/src/PHPMailer.php';
require '../vendor/phpmailer/src/SMTP.php';
require '../vendor/phpmailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include '../PHP/Config.php';

$response = ['success' => false, 'message' => ''];

// Only process POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nic = trim($_POST['nic'] ?? '');

    if (!$nic) {
        $response['message'] = 'Please enter your MOH / PHSRC Number.';
        echo json_encode($response);
        exit;
    }

    // Lookup patient
    $stmt = $conn->prepare("SELECT `Doctor ID`, `Email Address`, `First Name` FROM doctors WHERE `License Number` = ?");
    $stmt->bind_param("s", $nic);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        $patientID = $user['Doctor ID'];
        $userEmail = $user['Email Address'];
        $firstName = $user['First Name'];

        // Generate secure token
        $token = bin2hex(random_bytes(32));
        $hashedToken = password_hash($token, PASSWORD_DEFAULT);
        $expires = date("Y-m-d H:i:s", time() + 3600); // 1 hour from now

        // Insert token into DB
        // Instead of PHP date()
		$stmt2 = $conn->prepare("
			INSERT INTO password_resets (user_id, user_type, token_hash, expires_at) 
			VALUES (?, 'doctor', ?, NOW() + INTERVAL 1 HOUR)
		");
		$stmt2->bind_param("is", $patientID, $hashedToken);
		$stmt2->execute();

        // Create reset link
		$folder = rawurlencode("IAWD - CARELINK"); // Encodes space as %20, not +
		$resetLink = "http://localhost/$folder/PHP/ResetPasswordDoctor.php?token=$token&uid=$patientID";

        // Send email
        $mail = new PHPMailer(true);
        try {
            // SMTP settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'rjanudi@gmail.com'; // sender Gmail
            $mail->Password = 'ijcd yrmq mbub lhln'; // Gmail App Password
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('rjanudi@gmail.com', 'CareLink Support');
            $mail->addAddress($userEmail, $firstName);

            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request';
            $mail->Body = "
                <p>Hi $firstName,</p>
                <p>You requested a password reset. Click the link below to reset your password:</p>
                <p><a href='$resetLink'>Reset Password</a></p>
                <p>This link will expire in 1 hour.</p>
                <p>If you didn't request this, please ignore this email.</p>
            ";

            $mail->send();

        } catch (Exception $e) {
            // Do not reveal exact error to user
            $response['message'] = 'Unable to send email. Please try again later.';
            echo json_encode($response);
            exit;
        }

        $response['success'] = true;
        $response['message'] = 'If a doctor with that MOH / PHSRC Number exists, a password reset email has been sent.';
    } else {
        // Always show same message to prevent enumeration
        $response['success'] = true;
        $response['message'] = 'If a doctor with that MOH / PHSRC Number exists, a password reset email has been sent.';
    }

    echo json_encode($response);
}
?>
