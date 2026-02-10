<?php
session_start();
include '../PHP/Config.php';

$patient_id = $_SESSION['patient_id'] ?? null;
if (!$patient_id) {
    header("Location: ../HTML/Login.html");
    exit;
}

// Fetch patient details
$sql = "SELECT `First Name`, `Last Name`, `Phone Number`, `Email Address`, `Date of Birth`, `Blood Type`, `Allergies`, `Medical History`
        FROM patients 
        WHERE `PatientID` = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $patient = $result->fetch_assoc();
} else {
    echo "Patient details not found!";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Patient Dashboard - CareLink</title>
  <link rel="stylesheet" href="../CSS/header.css">
  <link rel="stylesheet" href="../CSS/footer.css">
  <link rel="stylesheet" href="../CSS/PatientDashboard.css">
  <style>
    .hidden { display: none; }
    .profile-card { max-width: 400px; margin: auto; padding: 20px; border: 1px solid #ccc; border-radius: 10px; }
    .profile-info p { margin: 8px 0; }
    .edit-profile-btn { padding: 8px 16px; cursor: pointer; border: none; background: #581845; color: #fff; border-radius: 5px; }
    .edit-profile-btn:hover { background: #7d1c6b; }
  </style>
</head>
<body>
<?php include '../HTML/Header.php'; ?>

<!-- Success Modal -->
<div id="successModal" class="modal hidden">
  <div class="modal-content">
    <span id="closeModal" class="close">&times;</span>
    <p id="successMessage">✅ Action completed successfully!</p>
  </div>
</div>


<main class="dashboard-container">

  <aside class="profile-card">
    <!-- Profile View -->
    <div id="profileView">
      <h2><?php echo $patient['First Name'].' '.$patient['Last Name']; ?></h2>
	  <br>
      <div class="profile-info">
        <p><strong>Email:</strong> <?php echo $patient['Email Address']; ?></p>
        <p><strong>Phone:</strong> <?php echo $patient['Phone Number']; ?></p>
        <p><strong>DOB:</strong> <?php echo $patient['Date of Birth'] ?? 'Not Set'; ?></p>
        <p><strong>Blood Type:</strong> <?php echo $patient['BloodType'] ?? 'Not Set'; ?></p>
        <p><strong>Allergies:</strong> <?php echo $patient['Allergies'] ?? 'None'; ?></p>
        <p><strong>Medical History:</strong> <?php echo $patient['MedicalHistory'] ?? 'None'; ?></p>
      </div>
	  <br>
      <div style="text-align:center; margin-top:15px;">
        <button type="button" id="editProfileBtn" class="edit-profile-btn">EDIT PROFILE</button>
      </div>
    </div>

    <!-- Edit Form -->
	<form id="profileForm" action="../HTML/UpdatePatientProfile.php" method="POST" class="hidden">
	  <p>Email: <input type="email" name="email" value="<?php echo htmlspecialchars($patient['Email Address']); ?>" required></p>
	  <p>Phone: <input type="text" name="phone" value="<?php echo htmlspecialchars($patient['Phone Number']); ?>" required></p>
	  <p>DOB: <input type="date" name="dob" value="<?php echo htmlspecialchars($patient['Date of Birth']); ?>"></p>

	  <p>Blood Type:
		<select name="blood_type">
		  <?php
		  $blood_types = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
		  foreach ($blood_types as $type) {
			  $selected = ($patient['BloodType'] ?? '') === $type ? 'selected' : '';
			  echo "<option value='$type' $selected>$type</option>";
		  }
		  ?>
		</select>
	  </p>

	  <p>Allergies:
		<select name="allergies">
		  <?php
		  $allergy_options = ['None', 'Peanuts', 'Dairy', 'Gluten', 'Seafood', 'Other'];
		  foreach ($allergy_options as $allergy) {
			  $selected = ($patient['Allergies'] ?? '') === $allergy ? 'selected' : '';
			  echo "<option value='$allergy' $selected>$allergy</option>";
		  }
		  ?>
		</select>
	  </p>

	  <p>Medical History: <textarea name="medical_history"><?php echo htmlspecialchars($patient['MedicalHistory'] ?? ''); ?></textarea></p>

	  <input type="hidden" name="patient_id" value="<?php echo $patient_id; ?>">
	  <div style="text-align:center; margin-top:10px; display: flex; justify-content: center; gap: 10px;">
		<button type="submit" class="edit-profile-btn" style="width: 120px;">SAVE</button>
		<button class="edit-profile-btn cancel-btn" onclick="cancelAppointment(${a.AppointmentID})">CANCEL</button>

	</div>

	</form>

  </aside>

  <!-- Main Dashboard -->
  <section class="dashboard-main">
    <div class="tabs">
	  <button class="tab-btn active" data-tab="myAppointments" style="font-weight:bold !important;">MY APPOINTMENTS</button>
	  <button class="tab-btn" data-tab="newAppointment" style="font-weight:bold !important;">BOOK APPOINTMENT</button>
	  <button class="tab-btn" data-tab="myReports" style="font-weight:bold !important;">MY REPORTS</button>
	</div>

    <!-- My Appointments -->
<div class="tab-content" id="myAppointments">
    <table id="appointmentsTable">
        <thead>
            <tr>
                <th>Date</th>
                <th>Time</th>
                <th>Doctor</th>
                <th>Specialization</th>
                <th>Facility</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <!-- Appointments load here -->
        </tbody>
    </table>
</div>

<!-- Book Appointment -->
<div id="newAppointment" class="tab-content">
    <form id="bookAppointmentForm" action="../PHP/CreateAppointment.php" method="POST">
     
        <!-- Facility Card -->
        <div style="background:#fdfdfd; border-radius:15px; padding:15px; margin-bottom:15px; box-shadow:0 4px 15px rgba(0,0,0,0.05); color: #000;">
            <label for="facilitySelect">Select Hospital/Facility:</label>
			<br><br>
            <select id="facilitySelect" name="facility_id" required
				style="width:100%; padding:12px 15px; border-radius:12px; border:1px solid #d3a6df; font-size:1rem; background:#fff; color:#000; appearance:none; -webkit-appearance:none; -moz-appearance:none; outline:none; background-image:url('data:image/svg+xml;charset=US-ASCII,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%2210%22 height=%206%22><path fill=%22%2358105d%22 d=%22M0 0l5 6 5-6z%22/></svg>'); background-repeat:no-repeat; background-position:right 12px center; background-size:10px 6px;">
				<option value="">Select Facility</option>
				<?php
					$facilities = $conn->query("SELECT `Facility ID`, `Facility Name` FROM facility");
					while($f = $facilities->fetch_assoc()){
						echo "<option value='{$f['Facility ID']}'>{$f['Facility Name']}</option>";
					}
				?>
			</select>
        </div>

        <!-- Doctor Card -->
        <div style="background:#fdfdfd; border-radius:15px; padding:15px; margin-bottom:15px; box-shadow:0 4px 15px rgba(0,0,0,0.05);">
            <label for="doctorSelect">Select Doctor:</label>
            <select id="doctorSelect" name="doctor_id" required>
                <option value="">Select Doctor</option>
            </select>
        </div>

        <!-- Date & Slots Card -->
        <div style="background:#fdfdfd; border-radius:15px; padding:15px; margin-bottom:15px; box-shadow:0 4px 15px rgba(0,0,0,0.05);">
            <label for="appointmentDate">Select Date:</label>
            <input type="date" id="appointmentDate" name="date" required> 
		</div>
		
		<div style="background:#fdfdfd; border-radius:15px; padding:15px; margin-bottom:15px; box-shadow:0 4px 15px rgba(0,0,0,0.05);">
            <label style="margin-top:10px;">Available Time Slots:</label>
            <div id="slotsContainer"></div>
            <input type="hidden" id="slotInput" name="time_slot">
        </div>

        <!-- Patient ID -->
        <input type="hidden" name="patient_id" value="<?php echo $patient_id; ?>">

        <!-- Submit Button -->
        <button type="submit" class="edit-profile-btn">Book Appointment</button>
    
    </form>
</div>


<!-- My Reports -->
<div id="myReports" class="tab-content">
  <table>
    <thead>
      <tr>
        <th>Date</th>
        <th>Test Type</th>
        <th>Notes</th>
        <th>View / Download</th>
      </tr>
    </thead>
    <tbody>
      <?php
        $rep_sql = "SELECT `ReportID`, `TestType`, `Notes`, `Attachments`, `TestDate`
                    FROM medical_reports
                    WHERE `PatientID` = ?
                    ORDER BY TestDate DESC";

        if($stmt = $conn->prepare($rep_sql)){
            $stmt->bind_param("i", $patient_id);
            $stmt->execute();
            $reports = $stmt->get_result();

            if($reports && $reports->num_rows > 0){
                while($r = $reports->fetch_assoc()){
                    $filePath = "../uploads/medical_reports/" . htmlspecialchars($r['Attachments']);
                    $testName = htmlspecialchars($r['TestType']);
                    $notes = htmlspecialchars($r['Notes']);
                    $date = htmlspecialchars($r['TestDate']);

                    echo "<tr>
                            <td>$date</td>
                            <td>$testName</td>
                            <td>$notes</td>
                            <td>
                                <a href='$filePath' target='_blank' class='edit-profile-btn'>View PDF</a>
                                <a href='$filePath' download class='edit-profile-btn' style='background:#2e86de;'>Download</a>
                            </td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='4' style='text-align:center;'>No reports available.</td></tr>";
            }
        } else {
            echo "<tr><td colspan='4' style='text-align:center;'>Unable to fetch reports.</td></tr>";
        }
      ?>
    </tbody>
  </table>
</div>



  </section>
</main>

<?php include '../HTML/Footer.php'; ?>
<script>
document.addEventListener("DOMContentLoaded", function() {

    // ----------------- Edit Profile Toggle -----------------
    const editBtn = document.getElementById("editProfileBtn");
    const profileView = document.getElementById("profileView");
    const profileForm = document.getElementById("profileForm");
    const cancelBtn = document.querySelector(".cancel-btn");

    if(editBtn && profileView && profileForm && cancelBtn){
        editBtn.addEventListener("click", () => {
            profileView.classList.add("hidden");
            profileForm.classList.remove("hidden");
        });

        cancelBtn.addEventListener("click", () => {
            profileForm.classList.add("hidden");
            profileView.classList.remove("hidden");
        });
    }

    // ----------------- Tabs -----------------
    const tabBtns = document.querySelectorAll(".tab-btn");
    const tabContents = document.querySelectorAll(".tab-content");

    tabBtns.forEach(btn => {
        btn.addEventListener("click", () => {
            tabContents.forEach(tc => tc.style.display = "none");
            tabBtns.forEach(b => b.classList.remove("active"));

            btn.classList.add("active");
            const tabId = btn.dataset.tab;
            const tab = document.getElementById(tabId);
            if(tab) tab.style.display = "block";

            if(tabId === "myAppointments") loadAppointments();
        });
    });

    // Show default tab
    document.querySelector(".tab-btn.active")?.click();

    // ----------------- Success Modal -----------------
    const urlParams = new URLSearchParams(window.location.search);
    if(urlParams.has('success')) {
        const modal = document.getElementById('successModal');
        const closeBtn = document.getElementById('closeModal');
        const messageEl = document.getElementById('successMessage');

        const type = urlParams.get('success');
        if(type === 'profile') messageEl.textContent = "✅ Profile updated successfully!";
        else if(type === 'booking') messageEl.textContent = "✅ Appointment booked successfully!";
        else messageEl.textContent = "✅ Action completed successfully!";

        modal.classList.remove('hidden');

        closeBtn.addEventListener('click', () => {
            modal.classList.add('hidden');
            urlParams.delete('success');
            window.history.replaceState({}, document.title, window.location.pathname);
        });

        setTimeout(() => {
            modal.classList.add('hidden');
            urlParams.delete('success');
            window.history.replaceState({}, document.title, window.location.pathname);
        }, 3000);
    }

    // ----------------- Dynamic Doctor Dropdown -----------------
    const facilitySelect = document.getElementById('facilitySelect');
    const doctorSelect = document.getElementById('doctorSelect');

    if(facilitySelect && doctorSelect){
        facilitySelect.addEventListener('change', function() {
            const facilityId = this.value;
            doctorSelect.innerHTML = '<option value="">Select Doctor</option>';
            if(!facilityId) return;

            fetch(`../PHP/GetDoctors.php?facility_id=${facilityId}`)
                .then(res => res.json())
                .then(data => {
                    if(data.length === 0){
                        doctorSelect.innerHTML = '<option value="">No doctors found</option>';
                        return;
                    }
                    data.forEach(doc => {
                        doctorSelect.innerHTML += `<option value="${doc.DoctorID}">Dr. ${doc.FirstName} ${doc.LastName} (${doc.Specialization})</option>`;
                    });
                })
                .catch(err => console.error("Error fetching doctors:", err));
        });
    }

    // ----------------- Load Available Slots -----------------
    const dateInput = document.getElementById('appointmentDate');
    const slotsContainer = document.getElementById('slotsContainer');
    const slotInput = document.getElementById('slotInput');

    async function loadSlots() {
        if(!doctorSelect.value || !dateInput.value){
            slotsContainer.innerHTML = '';
            slotInput.value = '';
            return;
        }

        try {
            const res = await fetch(`../PHP/GetSlots.php?doctor_id=${doctorSelect.value}&date=${dateInput.value}`);
            const data = await res.json();

            if(!data || data.length === 0){
                slotsContainer.innerHTML = '<p>No slots!</p>';
                slotInput.value = '';
                return;
            }

            slotsContainer.innerHTML = data.map(slot => {
                if(slot.status === "AVAILABLE"){
                    return `<button type="button" class="slot-btn slot-available" data-slot="${slot.time}">${slot.time}</button>`;
                } else {
                    return `<button type="button" class="slot-btn slot-booked" disabled>${slot.time}</button>`;
                }
            }).join('');

            slotsContainer.querySelectorAll('.slot-available').forEach(btn => {
                btn.addEventListener('click', () => {
                    slotsContainer.querySelectorAll('.slot-available').forEach(b => b.classList.remove('selected-slot'));
                    btn.classList.add('selected-slot');
                    slotInput.value = btn.dataset.slot;
                });
            });

        } catch(err){
            console.error('Error fetching slots:', err);
            slotsContainer.innerHTML = '<p>Error fetching slots.</p>';
        }
    }

    if(doctorSelect && dateInput && slotsContainer && slotInput){
        doctorSelect.addEventListener('change', loadSlots);
        dateInput.addEventListener('change', loadSlots);

        // Set min date to today
        const today = new Date().toISOString().split('T')[0];
        dateInput.setAttribute('min', today);
    }

    // ----------------- Load Appointments -----------------
    async function loadAppointments() {
        const tbody = document.querySelector("#appointmentsTable tbody");
        tbody.innerHTML = "<tr><td colspan='7'>Loading...</td></tr>";

        try {
            const res = await fetch("../PHP/GetAppointments.php");
            const data = await res.json();

            if(!data || data.length === 0){
                tbody.innerHTML = "<tr><td colspan='7' style='text-align:center;'>No appointments found.</td></tr>";
                return;
            }

            tbody.innerHTML = data.map(a => `
                <tr>
                    <td>${a.AppointmentDate}</td>
                    <td>${a.AppointmentTime}</td>
                    <td>Dr. ${a.DoctorFirstName} ${a.DoctorLastName}</td>
                    <td>${a.Specialization}</td>
                    <td>${a.FacilityName}</td>
                    <td>${a.Status}</td>
                    <td>
                        ${a.Status === 'CANCELLED' ? '' : `<button class="edit-profile-btn" onclick="cancelAppointment(${a.AppointmentID})">CANCEL</button>`}
                    </td>
                </tr>
            `).join('');

        } catch(err) {
            console.error(err);
            tbody.innerHTML = "<tr><td colspan='7'>Error loading appointments.</td></tr>";
        }
    }

    // ----------------- Cancel Appointment -----------------
    window.cancelAppointment = function(id) {
        if(!confirm("Are you sure you want to cancel this appointment?")) return;

        fetch(`../PHP/CancelAppointment.php?id=${id}`, { credentials: 'same-origin' })
            .then(res => res.text())
            .then(text => {
                try {
                    const data = JSON.parse(text);
                    alert(data.message);
                    if(data.success) loadAppointments();
                } catch(err) {
                    console.error('JSON parse error:', err, text);
                    alert('Server returned an invalid response.');
                }
            })
            .catch(err => {
                console.error('Fetch error:', err);
                alert('Something went wrong while cancelling.');
            });
    };

});
</script>


</body>
</html>
