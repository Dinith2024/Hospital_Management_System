<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include '../PHP/Config.php';
include '../HTML/DoctorSchedule.php';


// Check if doctor is logged in
$doctor_id = $_SESSION['doctor_id'] ?? null;
if (!$doctor_id) {
    header("Location: ../HTML/Login.html");
    exit;
}

// Fetch doctor details
$sql = "SELECT `Title`, `First Name`, `Last Name`, `Specialization`, `Qualifications`, `Phone Number`, `Email Address`, `Profile Image`
        FROM doctors 
        WHERE `Doctor ID` = ? LIMIT 1";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $doctor = $result->fetch_assoc();
    $profileImg = !empty($doctor['Profile Image']) ? '../' . $doctor['Profile Image'] : '';
} else {
    echo "Doctor details not found!";
    exit;
}

$search_nic = $_GET['search_nic'] ?? '';

$patients_sql = "
    SELECT p.`First Name`, p.`Last Name`, p.NIC, p.`Date of Birth`, MAX(a.`Date`) AS last_visit
    FROM appointments a
    JOIN patients p ON a.`PatientID` = p.`PatientID`
    WHERE a.`DoctorID` = ?
";

if (!empty($search_nic)) {
    $patients_sql .= " AND p.NIC LIKE ?";
}

$patients_sql .= " GROUP BY p.`PatientID` ORDER BY last_visit DESC";

$patients_stmt = $conn->prepare($patients_sql);

if (!empty($search_nic)) {
    $search_param = "%" . $search_nic . "%";
    $patients_stmt->bind_param("is", $doctor_id, $search_param);
} else {
    $patients_stmt->bind_param("i", $doctor_id);
}

$patients_stmt->execute();
$patients_result = $patients_stmt->get_result();

$patients = [];
while ($row = $patients_result->fetch_assoc()) {
    $dob = new DateTime($row['Date of Birth']);
    $today = new DateTime();
    $age = $today->diff($dob)->y;

    $patients[] = [
        'name' => $row['First Name'] . ' ' . $row['Last Name'],
        'nic' => $row['NIC'],
        'age' => $age,
        'last_visit' => $row['last_visit']
    ];
}

$appointments_sql = "
    SELECT a.`Date`, a.`Time`, p.`First Name`, p.`Last Name`, a.`Status`
    FROM appointments a
    JOIN patients p ON a.`PatientID` = p.`PatientID`
    WHERE a.`DoctorID` = ?
      AND a.`Date` >= CURDATE()
    ORDER BY a.`Date` ASC, a.`Time` ASC
";

$appointments_stmt = $conn->prepare($appointments_sql);
$appointments_stmt->bind_param("i", $doctor_id);
$appointments_stmt->execute();
$appointments_result = $appointments_stmt->get_result();

$appointments = [];
while($row = $appointments_result->fetch_assoc()) {
    $appointments[] = [
        'date' => $row['Date'],
        'time' => $row['Time'],
        'patient' => $row['First Name'] . ' ' . $row['Last Name'],
        'status' => $row['Status']
    ];
}

$upcoming_sql = "
    SELECT COUNT(*) AS upcoming_count
    FROM appointments
    WHERE `DoctorID` = ?
      AND `Date` >= CURDATE()
";

$upcoming_stmt = $conn->prepare($upcoming_sql);
$upcoming_stmt->bind_param("i", $doctor_id);
$upcoming_stmt->execute();
$upcoming_result = $upcoming_stmt->get_result();
$upcoming_count = 0;

if ($row = $upcoming_result->fetch_assoc()) {
    $upcoming_count = $row['upcoming_count'];
}

// Calculate total hours worked this week

$week_hours_sql = "
    SELECT SUM(TIMESTAMPDIFF(MINUTE, `Start Time`, `End Time`)) AS total_minutes
    FROM doctor_schedule
    WHERE `Doctor ID` = ?
      AND Day BETWEEN CURDATE() - INTERVAL WEEKDAY(CURDATE()) DAY
                  AND CURDATE() + INTERVAL (6 - WEEKDAY(CURDATE())) DAY
";

$stmt = $conn->prepare($week_hours_sql);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

$total_hours_week = 0;
if ($row && $row['total_minutes']) {
    $total_hours_week = round($row['total_minutes'] / 60, 2); // convert minutes to hours
}

// Initialize array with 7 days of the week
$week_hours = [
    'Monday' => 0,
    'Tuesday' => 0,
    'Wednesday' => 0,
    'Thursday' => 0,
    'Friday' => 0,
    'Saturday' => 0,
    'Sunday' => 0
];

// Fetch doctor's schedule
$schedule_sql = "
    SELECT `Day`, `Start Time`, `End Time`
    FROM doctor_schedule
    WHERE `Doctor ID` = ?
";
$stmt = $conn->prepare($schedule_sql);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();

while($row = $result->fetch_assoc()) {
    $day = $row['Day'];
    $start = strtotime($row['Start Time']);
	$end = strtotime($row['End Time']);
	$minutes = ($end - $start) / 60; // difference in minutes

    if(isset($week_hours[$day])) {
        $week_hours[$day] += round($minutes / 60, 2); // convert to hours
    }
}

$total_hours_week = array_sum($week_hours);

// Convert to JSON for Chart.js
$week_hours_json = json_encode(array_values($week_hours));
$week_days_json = json_encode(array_keys($week_hours));

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Doctor Dashboard - CareLink</title>
<link rel="stylesheet" href="../CSS/header.css">
<link rel="stylesheet" href="../CSS/footer.css">
<link rel="stylesheet" href="../CSS/DoctorDashboard.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<?php include '../HTML/Header.php'; ?>

<main class="dashboard-container">

  <!-- Profile Sidebar -->
  <aside class="profile-card">
    <!-- View Mode -->
    <div id="profileView">
      <div class="profile-img-container">
        <?php if($profileImg): ?>
          <img src="<?php echo $profileImg; ?>" alt="Doctor Photo" class="profile-img" id="profilePreviewView">
        <?php else: ?>
          <div class="default-avatar" id="profilePreviewView">
            <?php echo strtoupper($doctor['First Name'][0] . $doctor['Last Name'][0]); ?>
          </div>
        <?php endif; ?>
      </div>
      <br>
      <h2 id="doctorName"><?php echo $doctor['Title'] . ' ' . $doctor['First Name'] . ' ' . $doctor['Last Name']; ?></h2>
      <p id="doctorSpec"><?php echo $doctor['Specialization']; ?></p>
      <p id="doctorQual"><?php echo $doctor['Qualifications']; ?></p>
      <p id="doctorEmail">Email: <?php echo $doctor['Email Address']; ?></p>
      <p id="doctorPhone">Phone: <?php echo $doctor['Phone Number']; ?></p>
      <br>
      <button type="button" id="editProfileBtn" class="edit-profile-btn">EDIT PROFILE</button>
    </div>

    <!-- Edit Mode -->
    <form id="profileForm" action="../HTML/UpdateDoctorProfile.php" method="POST" enctype="multipart/form-data" style="display:none;">
      <div class="profile-img-container profile-hover">
        <?php if($profileImg): ?>
          <img src="<?php echo $profileImg; ?>" alt="Doctor Photo" class="profile-img" id="profilePreviewForm">
        <?php else: ?>
          <div class="default-avatar" id="profilePreviewForm">
            <?php echo strtoupper($doctor['First Name'][0] . $doctor['Last Name'][0]); ?>
          </div>
        <?php endif; ?>
        <input type="file" name="profileImage" id="profileImageForm" accept="image/*" style="display:none;">
        <div class="choose-text">Choose Image</div>
      </div>

      <h2><?php echo $doctor['Title'] . ' ' . $doctor['First Name'] . ' ' . $doctor['Last Name']; ?></h2>
      <p>Specialization: <input type="text" name="specialization" value="<?php echo $doctor['Specialization']; ?>"></p>
      <p>Qualifications: <input type="text" name="qualifications" value="<?php echo $doctor['Qualifications']; ?>"></p>
      <p>Email: <input type="email" name="email" value="<?php echo $doctor['Email Address']; ?>"></p>
      <p>Phone: <input type="text" name="phone" value="<?php echo $doctor['Phone Number']; ?>"></p>
      <input type="hidden" name="doctor_id" value="<?php echo $doctor_id; ?>">

      <button type="submit" class="edit-profile-btn">SAVE CHANGES</button>
      <button type="button" id="cancelEditBtn" class="edit-profile-btn" style="background:#aaa;margin-top:5px;">CANCEL</button>
    </form>
  </aside>

	<!-- Doctor Schedule Form -->
	<aside class="profile-card schedule-card">
	  <h3>SET YOUR SCHEDULE</h3>
	  <form action="../HTML/DoctorSchedule.php" method="POST" id="scheduleForm">
		<label for="day">Day:</label>
		<select name="day" id="day" required>
		  <option value="">Select Day</option>
		  <option value="Monday">Monday</option>
		  <option value="Tuesday">Tuesday</option>
		  <option value="Wednesday">Wednesday</option>
		  <option value="Thursday">Thursday</option>
		  <option value="Friday">Friday</option>
		  <option value="Saturday">Saturday</option>
		  <option value="Sunday">Sunday</option>
		</select>

		<label for="start_time">Start Time:</label>
		<input type="time" name="start_time" id="start_time" required>

		<label for="end_time">End Time:</label>
		<input type="time" name="end_time" id="end_time" required>

		<label for="slot_duration">Slot Duration (minutes):</label>
		<input type="number" name="slot_duration" id="slot_duration" min="5" max="120" value="30" required>

		<label for="num_slots">Number of Slots:</label>
		<input type="number" id="num_slots" value="5" disabled>
		<input type="hidden" name="num_slots_hidden" id="num_slots_hidden" value="5">

		<input type="hidden" name="doctor_id" value="<?php echo $doctor_id; ?>">

		<button type="submit" class="edit-profile-btn">SAVE SCHEDULE</button>
	  </form>
	</aside>

	

    <!-- Main Dashboard Content -->
    <section class="dashboard-main">

      <!-- Summary Stats -->
      <div class="stats-cards">
        <div class="stat-card">
		  <h4>TOTAL PATIENTS</h4>
		  <br>
		  <p><?= count($patients) ?></p>
		</div>
        <div class="stat-card">
		  <h4>UPCOMING APPOINTMENTS</h4>
		  <p><?= $upcoming_count ?></p>
		</div>
        <div class="stat-card">
		  <h4>TOTAL HOURS WORKED</h4>
		  <p><?= number_format($total_hours_week, 2) ?> HRS</p>
		</div>
      </div>

      <!-- Tabs -->
      <div class="tabs">
        <button class="tab-btn active" data-tab="patients">PATIENTS</button>
        <button class="tab-btn" data-tab="appointments">APPOINTMENTS</button>
        <button class="tab-btn" data-tab="hours-worked">HOURS WORKED</button>
		<button class="tab-btn" data-tab="manage-schedule">SCHEDULE</button>
      </div>

      <!-- Tab Content -->
      <div id="patients" class="tab-content active">
        <h3>MY PATIENTS</h3>
		<form method="GET" action="" style="margin-bottom:15px; display:flex; justify-content:flex-end; gap:5px; align-items:center;">
		  <input type="text" name="search_nic" placeholder="Search by NIC" value="<?= htmlspecialchars($_GET['search_nic'] ?? '') ?>" style="padding:5px; border-radius:5px; border:1px solid #d3a6df; width:200px;">
		  <button type="submit" class="edit-profile-btn" style="width:auto; padding:6px 12px; margin-left: 5px;">Search</button>
		</form>
        <table>
          <thead>
            <tr>
              <th>Name</th>
              <th>NIC</th>
              <th>Age</th>
              <th>Last Visit</th>
			  <th>Patient Reports</th>
            </tr>
          </thead>
          <tbody>
			<?php if(!empty($patients)): ?>
				<?php foreach($patients as $p): ?>
					<tr>
						<td><?= htmlspecialchars($p['name']) ?></td>
						<td><?= htmlspecialchars($p['nic']) ?></td>
						<td><?= $p['age'] ?></td>
						<td><?= $p['last_visit'] ?></td>
						<td style="text-align:center;">
						  <a href="../HTML/MedicalReport.php?nic=<?= urlencode($p['nic']) ?>" class="action-btn add-report-btn" title="Add Report">
							<i class="fa fa-upload" aria-hidden="true"></i>
						  </a>
						  <a href="../HTML/ViewMedicalReports.php?nic=<?= urlencode($p['nic']) ?>" class="action-btn view-records-btn" title="View Records" style = "margin-left: 10px;">
							<i class="fa fa-eye" aria-hidden="true"></i>
						</a>
						</td>
					</tr>
				<?php endforeach; ?>
			<?php else: ?>
				<tr><td colspan="4" style="text-align:center;">No patients found</td></tr>
			<?php endif; ?>
			</tbody>
        </table>
      </div>

      <div id="appointments" class="tab-content">
		<h3>MY APPOINTMENTS</h3>
		<table>
		  <thead>
			<tr>
			  <th>Date</th>
			  <th>Patient</th>
			  <th>Time</th>
			  <th>Status</th>
			</tr>
		  </thead>
		  <tbody>
			<?php if(!empty($appointments)): ?>
				<?php foreach($appointments as $a): ?>
					<tr>
						<td><?= htmlspecialchars($a['date']) ?></td>
						<td><?= htmlspecialchars($a['patient']) ?></td>
						<td><?= htmlspecialchars($a['time']) ?></td>
						<td><?= htmlspecialchars($a['status']) ?></td>
					</tr>
				<?php endforeach; ?>
			<?php else: ?>
				<tr><td colspan="4" style="text-align:center;">No upcoming appointments</td></tr>
			<?php endif; ?>
		  </tbody>
		</table>
	  </div>

      <div id="hours-worked" class="tab-content">
        <h3>HOURS WORKED THIS WEEK</h3>
        <canvas id="hoursChart" height="250"></canvas>
      </div>
	  
	  <div id="manage-schedule" class="tab-content">
	  <h3>My Schedules</h3>
	  <table>
		<thead>
		  <tr>
			<th>Day</th>
			<th>Start Time</th>
			<th>End Time</th>
			<th>Slot Duration</th>
			<th>Number of Slots</th>
			<th>Action</th>
		  </tr>
		</thead>
		<tbody>
		  <?php if(!empty($schedules)): ?>
			<?php foreach($schedules as $s): ?>
			  <tr>
				<td><?= $s['Day'] ?></td>
				<td><?= $s['Start Time'] ?></td>
				<td><?= $s['End Time'] ?></td>
				<td><?= $s['Slot Duration'] ?></td>
				<td><?= $s['Number of Slots'] ?></td>
				<td>
				  <button class="action-btn edit-btn" data-id="<?= $s['ID'] ?>"><i class="fa fa-edit"></i></button>
				  <button class="action-btn delete-btn" data-id="<?= $s['ID'] ?>">
					<i class="fa fa-trash"></i>
				</button>
				</td>
			  </tr>
			<?php endforeach; ?>
		  <?php else: ?>
			<tr><td colspan="6" style="text-align:center;">No schedules found</td></tr>
		  <?php endif; ?>
		</tbody>
	  </table>
	</div>


    </section>
  </main>

	<!-- Schedule Added Modal -->
	<div id="scheduleModal" class="modal">
	  <div class="modal-content">
		<span class="close-btn">&times;</span>
		<p>Schedule added successfully!</p>
	  </div>
	</div>

  <!-- Footer -->
  <?php include '../HTML/Footer.php'; ?>

  <script src="../JS/DoctorDashboardScript.js"></script>
  <script>
    // Chart.js for hours worked
    const ctx = document.getElementById('hoursChart').getContext('2d');
	const hoursChart = new Chart(ctx, {
	  type: 'bar',
	  data: {
		labels: <?= $week_days_json ?>, // Mon, Tue, ...
		datasets: [{
		  label: 'Hours Worked',
		  data: <?= $week_hours_json ?>, // hours per day
		  backgroundColor: 'rgba(235, 95, 231, 0.7)',
		  borderColor: '#58105d',
		  borderWidth: 1,
		  borderRadius: 8
		}]
	  },
	  options: {
		responsive: true,
		plugins: { legend: { display: false } },
		scales: {
		  y: { beginAtZero: true, title: { display: true, text: 'Hours' } },
		  x: { title: { display: true, text: 'Day' } }
		}
	  }
	});
  </script>
  
  <script>
	  // Get modal elements
	  const modal = document.getElementById("scheduleModal");
	  const closeBtn = modal.querySelector(".close-btn");

	  // Show modal if URL has success=1
	  const urlParams = new URLSearchParams(window.location.search);
	  if(urlParams.get('success') === '1') {
		modal.style.display = "block";

		// Remove the query parameter from URL without reload
		window.history.replaceState({}, document.title, window.location.pathname);
	  }

	  // Close modal on clicking X
	  closeBtn.onclick = () => {
		modal.style.display = "none";
	  }

	  // Close modal if clicked outside content
	  window.onclick = (e) => {
		if(e.target === modal) modal.style.display = "none";
	  }
	</script>

</body>
</html>
