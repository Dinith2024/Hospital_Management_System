<?php
session_start();
include '../PHP/Config.php';

$doctor_id = $_SESSION['doctor_id'] ?? null;
if (!$doctor_id) {
    header("Location: ../HTML/Login.html");
    exit;
}

// Get doctor details
$doc_sql = "SELECT `Title`, `First Name`, `Last Name` FROM doctors WHERE `Doctor ID` = ?";
$stmt = $conn->prepare($doc_sql);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$doc_res = $stmt->get_result();
$doctor = $doc_res->fetch_assoc();
$doctor_name = $doctor ? $doctor['Title'].' '.$doctor['First Name'].' '.$doctor['Last Name'] : '';

// Get patient details from NIC
$patient_name = '';
$patient_id = '';
if (isset($_GET['nic'])) {
    $nic = $_GET['nic'];
    $pat_sql = "SELECT `PatientID`, `First Name`, `Last Name` FROM patients WHERE NIC = ?";
    $pstmt = $conn->prepare($pat_sql);
    $pstmt->bind_param("s", $nic);
    $pstmt->execute();
    $pat_res = $pstmt->get_result();
    if ($row = $pat_res->fetch_assoc()) {
        $patient_name = $row['First Name'].' '.$row['Last Name'];
        $patient_id = $row['PatientID'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CareLink | Request Medical Report</title>
  <link rel="stylesheet" href="../CSS/MedicalReport.css">
</head>
<body>

<div id="header-placeholder"></div>
<br><br>
<main class="contact-container">
  <div class="contact-card">
    <div class="contact-left">
      <img src="../IMAGES/medreport.webp" alt="Medical Report Illustration">
    </div>
	
    <div class="contact-right">
      <h1>Request Medical Report</h1>
      <p>Please fill out the form below to upload the patient's medical report.</p>

      <form class="form" id="reportForm" enctype="multipart/form-data">
        <div class="form-group">
			<label>Test Type</label>
			<select name="test_type" id="testType" required>
				<option value="" disabled selected>Select Test Type</option>
				<option value="Blood Test">Blood Test</option>
				<option value="X-Ray">X-Ray</option>
				<option value="MRI">MRI</option>
				<option value="Ultrasound">Ultrasound</option>
				<option value="ECG">ECG</option>
			</select>
		</div>

        <div class="form-group">
          <label>Test Date</label>
          <input type="date" name="test_date" id="testDate" required>
        </div>
	
        <div class="form-group">
          <label>Priority</label>
          <select name="priority" id="priority" required>
            <option value="">Select Priority</option>
            <option value="Normal">Normal</option>
            <option value="Urgent">Urgent</option>
          </select>
        </div>
		
		<div class="form-group">
		  <label>Patient's Name</label>
		  <input type="text" name="patient_name" value="<?= htmlspecialchars($patient_name) ?>" readonly>
		  <input type="hidden" name="patient_id" value="<?= htmlspecialchars($patient_id) ?>">
		</div>

		<div class="form-group">
		  <label>Doctor's Name</label>
		  <input type="text" name="doctor_name" value="<?= htmlspecialchars($doctor_name) ?>" readonly>
		  <input type="hidden" name="doctor_id" value="<?= htmlspecialchars($doctor_id) ?>">
		</div>

        <div class="form-group">
          <label>Test Notes / Instructions (Optional)</label>
          <textarea name="notes" id="testNotes" rows="3"></textarea>
        </div>

        <div class="form-group">
          <label>Upload Attachment</label>
          <input type="file" name="attachment" id="fileInput" required>
        </div>

        <button type="submit" class="btn">
          <span>SUBMIT REPORT</span>
          <div class="loader-circle"></div>
        </button>
      </form>
    </div>
  </div>
</main>

<!-- Success Modal -->
<div id="successModal" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
    <h2>Request Submitted!</h2>
    <p>Your medical report has been uploaded successfully!</p>
  </div>
</div>

<div id="footer-placeholder"></div>

<script>
  fetch('../HTML/header.php')
    .then(res => res.text())
    .then(data => document.getElementById('header-placeholder').innerHTML = data);

  fetch('../HTML/footer.php')
    .then(res => res.text())
    .then(data => document.getElementById('footer-placeholder').innerHTML = data);
</script>

<script src="../JS/MedicalReport.js"></script>
</body>
</html>
