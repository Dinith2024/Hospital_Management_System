<?php
session_start();
include '../PHP/Config.php';

$error = '';
$showForm = false;
$patient_id = null;

// --- DEBUG ---
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if token & uid are provided
if (isset($_GET['token'], $_GET['uid'])) {
    $token = $_GET['token'];
    $uid = intval($_GET['uid']);

    // Fetch latest reset for this patient
    $stmt = $conn->prepare("
        SELECT * FROM password_resets 
        WHERE user_id = ? AND user_type = 'patient' 
        ORDER BY created_at DESC 
        LIMIT 1
    ");
    $stmt->bind_param("i", $uid);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $reset = $result->fetch_assoc();

        // DEBUG
        // var_dump($reset);

        // Check expiration
        $expires_at = strtotime($reset['expires_at']);
        if (time() > $expires_at) {
            $error = "This reset link has expired.";
        } else {
            // Compare SHA256 of token
            if (password_verify($token, $reset['token_hash'])) {
                $showForm = true;
                $patient_id = $uid;
            } else {
                $error = "Invalid token.";
            }
        }
    } else {
        $error = "Invalid or expired token.";
    }
} else {
    $error = "Invalid or expired token.";
}

// Handle POST form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patient_id = intval($_POST['patient_id']);
    $new_pass = $_POST['password'] ?? '';
    $confirm_pass = $_POST['confirm_password'] ?? '';

    if ($new_pass !== $confirm_pass) {
        $error = "Passwords do not match.";
        $showForm = true;
    } elseif (strlen($new_pass) < 6) {
        $error = "Password must be at least 6 characters.";
        $showForm = true;
    } else {
        // Hash new password
        $hashed = password_hash($new_pass, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE patients SET Password = ? WHERE PatientID = ?");
        $stmt->bind_param("si", $hashed, $patient_id);

        if ($stmt->execute()) {
            // Delete used tokens
            $stmt = $conn->prepare("DELETE FROM password_resets WHERE user_id = ? AND user_type = 'patient'");
            $stmt->bind_param("i", $patient_id);
            $stmt->execute();

            $_SESSION['success'] = "Password updated successfully! You can now log in.";
            header("Location: ../HTML/Login.html");
            exit;
        } else {
            $error = "Failed to update password. Try again.";
            $showForm = true;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Reset Password - CareLink</title>
<link rel="stylesheet" href="../CSS/style.css">
<style>
body { font-family: 'Segoe UI', sans-serif; background: #f6f6f6; }
.login-container { display: flex; max-width: 900px; margin: 50px auto; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.1); min-height: 500px; }
.login-left { flex: 1; background: #f0f0f0; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 40px; }
.login-left img.logo { width: 180px; margin-bottom: 25px; }
.login-left img { width: 90%; max-width: 260px; }
.login-right { flex: 1; padding: 50px; display: flex; flex-direction: column; justify-content: center; }
.login-right h1 { text-align: center; margin-bottom: 30px; font-size: 28px; }
input[type=password] { width: 100%; padding: 14px 12px; margin: 10px 0; border-radius: 8px; border: 1px solid #ccc; font-size: 17px; }
.btn { width: 100%; padding: 14px; background: linear-gradient(135deg, #eb5fe7, #581d9b); color: #fff; border: none; border-radius: 8px; font-size: 17px; cursor: pointer; margin-top: 12px; transition: background 0.3s ease; }
.btn:hover { background: linear-gradient(135deg, #d14fd0, #43157a); }
.switch-text { text-align: center; margin-top: 20px; font-size: 16px; }
.switch-text a { text-decoration: none; color: #581d9b; font-weight: 500; }

.modal { display: <?= $error ? 'flex' : 'none' ?>; position: fixed; top:0; left:0; width:100%; height:100%; background: rgba(0,0,0,0.5); justify-content:center; align-items:center; z-index: 9999; }
.modal-content { background:#fff; padding:25px; border-radius:12px; max-width:400px; width:90%; text-align:center; box-shadow: 0 4px 20px rgba(0,0,0,0.2); }
.modal-content p { margin-bottom: 20px; font-weight:600; color:#b00020; font-size:16px; }
.modal-content button { padding:12px 25px; border:none; border-radius:8px; background: linear-gradient(135deg, #eb5fe7, #581d9b); color:#fff; cursor:pointer; font-size:16px; }
.modal-content button:hover { background: linear-gradient(135deg, #d14fd0, #43157a); }
</style>
</head>
<body>

<div class="login-container">
  <div class="login-left">
    <img src="../Images/logo1.png.png" alt="CareLink Logo" class="logo">
    <img src="../Images/doctor-photo.png" alt="Illustration">
  </div>

  <div class="login-right">
    <h1>Reset Password</h1>
	
    <?php if ($showForm): ?>

        <form method="POST">
            <input type="hidden" name="patient_id" value="<?= $patient_id ?>">
            <label>New Password</label>
            <input type="password" name="password" placeholder="Enter new password" required>
            <label>Confirm Password</label>
            <input type="password" name="confirm_password" placeholder="Confirm new password" required>
            <button type="submit" class="btn">UPDATE PASSWORD</button>
        </form>
        <p class="switch-text"><a href="../HTML/Login.html">BACK TO LOGIN</a></p>
		<?php else: ?>

        <p class="switch-text"><a href="../HTML/Login.html">Back to login</a></p>
    <?php endif; ?>
  </div>
</div>
<?php if ($error): ?>

<div id="errorModal" class="modal">

    <div class="modal-content">

        <p><?= htmlspecialchars($error) ?></p>

        <button onclick="document.getElementById('errorModal').style.display='none'">OK</button>

    </div>

</div>

<?php endif; ?>

</body>
</html>
