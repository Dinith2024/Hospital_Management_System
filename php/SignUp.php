<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>CareLink - Sign Up</title>
  <link rel="stylesheet" href="../CSS/style.css"/>
</head>
<body>
  <div class="signup-container">
    <!-- Left side illustration -->
    <div class="signup-left">
      <!--<h2>WELCOME TO</h2> -->
      <img src="../IMAGES/logo1.png.png" alt="CareLink Logo" class="logo">
      <img src="../IMAGES/nurse1.png.png" alt="A friendly nurse illustration">
    </div>

    <!-- Right side form -->
    <div class="signup-right">
      <h1>SIGN UP</h1>

      <div class="tabs">
        <button class="tab-btn active" data-tab="patient-form">PATIENT</button>
        <button class="tab-btn" data-tab="clinic-form">HEALTH FACILITY</button>
		<button class="tab-btn" data-tab="doctor-form">DOCTOR</button>
      </div>

      <!-- Clinic Sign Up -->
      <form id="clinic-form" class="form hidden" method="POST" action="../PHP/SignUp_Facility.php">
        <h3>FACILITY INFORMATION</h3>
		<label>FACILITY TYPE</label>
		<select name="facility_type" name="facility_type" required>
		  <option value="" disabled selected>Select Facility Type</option>
		  <option value="Clinic">CLINIC</option>
		  <option value="Hospital">HOSPITAL</option>
		  <option value="Lab">LAB</option>
		</select>

		<label>SECTOR</label>
		<select name="sector" required>
		  <option value="" disabled selected>Select Sector</option>
		  <option value="Private">PRIVATE</option>
		  <option value="Government">GOVERNMENT</option>
		</select>
        <label>FACILITY NAME</label>
        <input type="text" name="facility_name" placeholder="Enter Name" required/>
        <label>MOH / PHSRC NUMBER</label>
        <input type="text" name="registration_no" placeholder="Enter Registration No." required/>
        <label>ADDRESS</label>
        <input type="text" name="address" placeholder="Enter Address" required/>

        <h3>CONTACT INFORMATION</h3>
        <label>PHONE NUMBER</label>
        <input type="tel" name="phone_number" placeholder="Enter Phone Number" required/>
        <label>EMAIL ADDRESS</label>
        <input type="email" name="email" placeholder="Enter Email Address" required/>

        <h3>CREDENTIALS</h3>
        <label>PASSWORD</label>
        <input type="password" name="password" placeholder="Enter Password" required/>
        <label>CONFIRM PASSWORD</label>
        <input type="password" name="confirm_password" placeholder="Confirm Password" required/>

        <button type="submit" class="btn">SIGN UP</button>
      </form>

      <!-- Patient Sign Up -->
      <form id="patient-form" class="form active" method="POST" action="../PHP/SignUp_Patient.php">
        <h3>PERSONAL INFORMATION</h3>
        <label>TITLE</label>
        <select name="title" required>
          <option value="" disabled selected>Select Title</option>
          <option value="Mr">MR.</option>
          <option value="Ms">MRS.</option>
          <option value="Dr">MS.</option>
        </select>
        <label>FIRST NAME</label>
        <input type="text" name="first_name" placeholder="Enter First Name" required/>
        <label>LAST NAME</label>
        <input type="text" name="last_name" placeholder="Enter Last Name" required/>
        <label>NIC NUMBER</label>
        <input type="text" name="nic_number" placeholder="Enter NIC NUMBER" 
         pattern="^(\d{9}[vV]|\d{12})$" 
         title="Enter valid NIC: 9 digits + V (pre-2016) or 12 digits (post-2016)" 
         required/>
        <h3>CONTACT INFORMATION</h3>
        <label>PHONE NUMBER</label>
        <input type="tel" name="phone_number" placeholder="Enter Phone Number" 
         pattern="^\d{10}$" 
         title="Enter 10-digit phone number" 
         required/>
        <label>EMAIL ADDRESS</label>
        <input type="email" name="email" placeholder="Enter Email Address" required/>

        <h3>CREDENTIALS</h3>
        <label>PASSWORD</label>
        <input type="password" name="password" placeholder="Enter Password" required/>
        <label>CONFIRM PASSWORD</label>
        <input type="password" name="confirm_password" placeholder="Confirm Password" required/>

        <button type="submit" class="btn">SIGN UP</button>
      </form>
	  
	  <!-- Doctor Sign Up -->
      <form id="doctor-form" class="form hidden" method="POST" action="../PHP/SignUp_Doctor.php">
        <h3>PERSONAL INFORMATION</h3>
        <label>TITLE</label>
        <select name="title" required>
          <option value="" disabled selected>Select Title</option>
          <option value="Mr">MR.</option>
          <option value="Ms">MRS.</option>
          <option value="Dr">MS.</option>
        </select>
        <label>FIRST NAME</label>
        <input type="text" name="first_name" placeholder="Enter First Name" required/>
        <label>LAST NAME</label>
        <input type="text" name="last_name" placeholder="Enter Last Name" required/>
        <label>NIC NUMBER</label>
        <input type="text" name="nic_number" placeholder="Enter NIC NUMBER" 
         pattern="^(\d{9}[vV]|\d{12})$" 
         title="Enter valid NIC: 9 digits + V (pre-2016) or 12 digits (post-2016)" 
         required/>
		<h3>PROFESSIONAL INFORMATION</h3>
		<label>FACILITY</label>
		<select name="facility_name" required>
		  <option value="" disabled selected>Select Facility</option>
		  <?php
			include '../PHP/Config.php'; 
			$sql = "SELECT `Facility ID` AS facility_id, `Facility Name` AS facility_name  FROM facility ORDER BY `Facility Name`";
			$result = $conn->query($sql);
			if ($result->num_rows > 0) {
				while($row = $result->fetch_assoc()) {
					echo "<option value='" . $row['facility_id'] . "'>" . htmlspecialchars($row['facility_name']) . "</option>";
				}
			} else {
				echo "<option disabled>No facilities registered yet</option>";
			}
		  ?>
		</select>
		<label>MEDICAL LICENSE NUMBER</label>
		<input type="text" name="license_number" placeholder="Enter License Number" required/>

		<label>SPECIALIZATION</label>		
		<input type="text" name="specialization" placeholder="Enter Specialization (e.g., Cardiologist)" required/>

		<label>YEARS OF EXPERIENCE</label>
		<input type="number" name="experience" placeholder="Enter Years of Experience" min="0" required/>

		<label>QUALIFICATIONS</label>
		<textarea name="qualifications" placeholder="Enter Qualifications (e.g., MBBS, MD)" rows="3"></textarea>

        <h3>CONTACT INFORMATION</h3>
        <label>PHONE NUMBER</label>
        <input type="tel" name="phone_number" placeholder="Enter Phone Number" 
         pattern="^\d{10}$" 
         title="Enter 10-digit phone number" 
         required/>
        <label>EMAIL ADDRESS</label>
        <input type="email" name="email" placeholder="Enter Email Address" required/>

        <h3>CREDENTIALS</h3>
        <label>PASSWORD</label>
        <input type="password" name="password" placeholder="Enter Password" required/>
        <label>CONFIRM PASSWORD</label>
        <input type="password" name="confirm_password" placeholder="Confirm Password" required/>

        <button type="submit" class="btn">SIGN UP</button>
      </form>

      <p class="switch-text">Already have an account? <a href="login.html">Login</a></p>
    </div>
  </div>

  <script src="../JS/script.js"></script>
  
  <script>
document.addEventListener("DOMContentLoaded", () => {
  // Show correct tab if URL has a hash, e.g., #doctor-form
  const hash = window.location.hash; // e.g., #doctor-form
  if (hash) {
    const targetForm = document.querySelector(hash);
    if (targetForm) {
      // Hide all forms
      document.querySelectorAll(".form").forEach(f => f.classList.add("hidden"));
      // Remove active class from all tabs
      document.querySelectorAll(".tab-btn").forEach(t => t.classList.remove("active"));
      // Show target form
      targetForm.classList.remove("hidden");
      // Activate corresponding tab button
      const targetTab = document.querySelector(`.tab-btn[data-tab='${hash.substring(1)}']`);
      if (targetTab) targetTab.classList.add("active");
    }
  }
});
</script>

</body>
</html>
