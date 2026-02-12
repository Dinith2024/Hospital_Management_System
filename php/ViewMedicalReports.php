<?php

session_start();
include '../PHP/Config.php';

$doctor_id = $_SESSION['doctor_id'] ?? null;
if (!$doctor_id) {
    header("Location: ../HTML/Login.html");
    exit;
}

$patient_name = '';
$patient_id = '';
if (isset($_GET['nic'])) {
    $nic = $_GET['nic'];
    $pat_sql = "SELECT `PatientID`, `First Name`, `Last Name` FROM patients WHERE NIC = ?";
    $stmt = $conn->prepare($pat_sql);
    $stmt->bind_param("s", $nic);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        $patient_name = $row['First Name'] . ' ' . $row['Last Name'];
        $patient_id = $row['PatientID'];
    }
}

// Fetch medical reports
$reports = [];
if ($patient_id) {
    $reports_sql = "SELECT * FROM medical_reports WHERE PatientID = ? ORDER BY TestDate DESC";
    $stmt = $conn->prepare($reports_sql);
    $stmt->bind_param("i", $patient_id);
    $stmt->execute();
    $reports_res = $stmt->get_result();
    if ($reports_res) {
        $reports = $reports_res->fetch_all(MYSQLI_ASSOC);
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Medical Reports - <?= htmlspecialchars($patient_name) ?></title>
<link rel="stylesheet" href="../CSS/header.css">
<link rel="stylesheet" href="../CSS/footer.css">
<link rel="stylesheet" href="../CSS/DoctorDashboard.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
.dashboard-container {
    display: flex;
    justify-content: center;
    padding: 40px 20px;
}

.reports-container {
    max-width: 1000px;
    margin: 40px auto;
    padding: 20px;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}
h2 {
    text-align: center;
    margin-bottom: 20px;
}
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
}
th, td {
    padding: 12px;
    border-bottom: 1px solid #ddd;
    text-align: left;
}
th {
    background: #eb5fe7;
    color: #fff;
    font-weight: 600;
}
a.download-btn {
    color: #58105d;
    text-decoration: none;
    font-weight: bold;
}
a.download-btn:hover {
    text-decoration: underline;
}
.no-reports {
    text-align: center;
    padding: 20px;
    color: #555;
}

/* Modal box */
.report-modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.6);
    justify-content: center;
    align-items: center;
}

/* Modal box */
.report-modal-content {
    background: #fff;
    padding: 25px 30px;
    border-radius: 16px;
    max-width: 500px;
    width: 90%;
    box-shadow: 0 8px 25px rgba(0,0,0,0.2);
    animation: fadeIn 0.3s ease;
    position: relative; /* for the close button */
}

/* Title */
.report-modal-content h3 {
    margin-bottom: 15px;
    text-align: center;
    font-size: 20px;
    color: #58105d;
}

/* Close button */
.report-modal-content .close {
    position: absolute;
    top: 12px;
    right: 16px;
    font-size: 22px;
    font-weight: bold;
    color: #888;
    cursor: pointer;
}

.report-modal-content {
    z-index: 1001; /* higher than modal overlay */
}

.report-modal-content .close:hover {
    color: #eb5fe7;
}

/* Form styling */
#editReportForm label {
    display: block;
    margin: 10px 0 5px;
    font-weight: 600;
    color: #333;
}

#editReportForm input,
#editReportForm select,
#editReportForm textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 8px;
    font-size: 14px;
    margin-bottom: 12px;
}

#editReportForm textarea {
    resize: none;
}

#editReportForm .btn {
    display: block;
    width: 100%;
    padding: 12px;
    background: #eb5fe7;
    color: #fff;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    font-weight: bold;
    transition: 0.2s ease;
    position: relative;   /* added */
    z-index: 1002;        /* higher than modal content */
}


/* Animation */
@keyframes fadeIn {
    from { opacity: 0; transform: scale(0.95); }
    to { opacity: 1; transform: scale(1); }
}

</style>
</head>
<body>

<?php include '../HTML/Header.php'; ?>

<main class="dashboard-container">
    <section class="tab-content active">
        <div class="reports-container">
            <h2 style="text-transform: uppercase;">Medical Reports for <?= htmlspecialchars($patient_name) ?></h2>

            <?php if (!empty($reports)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Test Type</th>
                            <th>Date</th>
                            <th>Priority</th>
                            <th>Notes</th>
                            <th>Attachment</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($reports as $r): ?>
                            <tr>
                                <td><?= htmlspecialchars($r['TestType']) ?></td>
                                <td><?= htmlspecialchars($r['TestDate']) ?></td>
                                <td><?= htmlspecialchars($r['Priority']) ?></td>
                                <td><?= htmlspecialchars($r['Notes']) ?></td>
                                <td>
                                    <?php if (!empty($r['Attachments'])): ?>
                                        <a class="download-btn" href="../uploads/medical_reports/<?= htmlspecialchars($r['Attachments']) ?>" download>
                                            <i class="fa fa-download"></i> Download
                                        </a>
                                    <?php else: ?>
                                        No file
                                    <?php endif; ?>
                                </td>
                                <td style="text-align:center;">
                                    <a href="#" class="edit-report-btn" data-id="<?= $r['ReportID'] ?>" title="Edit Report">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <a href="../HTML/DeleteMedicalReport.php?id=<?= $r['ReportID'] ?>&nic=<?= $nic ?>" 
									   class="delete-report-btn" 
									   onclick="return confirm('Are you sure you want to delete this report?');" 
									   title="Delete Report">
									   <i class="fa fa-trash"></i>
									</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="no-reports">No medical reports available for this patient.</div>
            <?php endif; ?>
        </div>

        <!-- Edit Modal -->
        <div id="editReportModal" class="report-modal">
			<div class="report-modal-content">
                <span class="close">&times;</span>
                <h3>Edit Medical Report</h3>
                <form id="editReportForm" enctype="multipart/form-data">
                    <input type="hidden" name="report_id" id="editReportId">

                    <label>Test Date:</label>
					<input type="date" name="test_date" id="editTestDate" required>

                    <label>Priority:</label>
                    <select name="priority" id="editPriority" required>
                        <option value="Normal">Normal</option>
                        <option value="Urgent">Urgent</option>
                    </select>

                    <label>Notes:</label>
                    <textarea name="notes" id="editNotes" rows="3"></textarea>

                    <label>Attachment (current: <span id="currentAttachment"></span>):</label>
                    <input type="file" name="attachment" id="editAttachment">

                    <button type="submit" class="btn">Save Changes</button>
                </form>
            </div>
        </div>

    </section>
</main>

<?php include '../HTML/Footer.php'; ?>

<script>
// Pass PHP reports array to JS safely
const reportsData = <?= json_encode($reports, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;

const editModal = document.getElementById('editReportModal');
const closeEditModal = editModal.querySelector('.close');

// REMOVE this line 👇
// editModal.style.display = 'flex';

document.querySelectorAll('.edit-report-btn').forEach(btn => {
    btn.addEventListener('click', e => {
        e.preventDefault();
        const report = reportsData.find(r => r.ReportID == btn.dataset.id);

        document.getElementById('editReportId').value = report.ReportID;// format to YYYY-MM-DD
		const formattedDate = report.TestDate.split(' ')[0];  
		document.getElementById('editTestDate').value = formattedDate;
        document.getElementById('editPriority').value = report.Priority;
        document.getElementById('editNotes').value = report.Notes;
        document.getElementById('currentAttachment').innerText = report.Attachments || 'None';

        editModal.style.display = 'flex'; // ✅ only open when edit clicked
    });
});

// close modal
closeEditModal.onclick = () => { editModal.style.display = 'none'; }
window.onclick = (e) => { if (e.target === editModal) editModal.style.display = 'none'; }


document.getElementById('editReportForm').addEventListener('submit', function(e){
    e.preventDefault();
    const formData = new FormData(this);

    fetch('../HTML/EditMedicalReport.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if(data.success){
            alert('Report updated!');
            location.reload();
        } else {
            alert(data.message);
        }
    })
    .catch(err => console.error(err));
});
</script>

<script>
// Disable future dates for the Test Date input
const today = new Date().toISOString().split('T')[0];
const editDateInput = document.getElementById('editTestDate');
editDateInput.setAttribute('max', today);
</script>


</body>
</html>
