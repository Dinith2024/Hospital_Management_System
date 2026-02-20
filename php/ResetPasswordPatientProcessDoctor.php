<?php
session_start();
include '../PHP/Config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (!$token || !$password || !$confirmPassword) {
        die("All fields are required.");
    }

    if ($password !== $confirmPassword) {
        die("Passwords do not match.");
    }

    $tokenHash = hash('sha256', $token);

    // Verify token
    $stmt = $conn->prepare("SELECT * FROM password_resets WHERE token_hash = ? AND used = 0 AND expires_at >= NOW() AND user_type='doctor'");
    $stmt->bind_param("s", $tokenHash);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows !== 1) {
        die("Invalid or expired token.");
    }

    $reset = $result->fetch_assoc();
    $userId = $reset['user_id'];

    // Update password
    $newPasswordHash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE doctors SET Password = ? WHERE `Doctor ID` = ?");
    $stmt->bind_param("si", $newPasswordHash, $userId);
    $stmt->execute();

    // Mark token as used
    $stmt = $conn->prepare("UPDATE password_resets SET used = 1 WHERE id = ?");
    $stmt->bind_param("i", $reset['id']);
    $stmt->execute();

    echo "<script>alert('Password reset successful!'); window.location='../HTML/Login.html';</script>";
}
?>
