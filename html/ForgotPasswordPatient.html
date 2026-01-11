<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Forgot Password - CareLink</title>
<link rel="stylesheet" href="../CSS/style.css"/>
<style>
/* Modal styles */
.modal {
  display: none; 
  position: fixed; 
  z-index: 1000; 
  left: 0; top: 0; width: 100%; height: 100%; 
  background-color: rgba(0,0,0,0.5); 
  justify-content: center; 
  align-items: center;
}

.modal-content {
  background: #fff;
  padding: 20px;
  border-radius: 12px;
  text-align: center;
  max-width: 400px;
  margin: auto;
}

.modal-content button {
  margin-top: 15px;
  padding: 10px 20px;
  background: #581d9b;
  color: #fff;
  border: none;
  border-radius: 6px;
  cursor: pointer;
}

.modal-content button:hover {
  background: #43157a;
}
</style>
</head>
<body>

<div class="login-container">
  <div class="login-left">
    <img src="../Images/logo1.png.png" alt="CareLink Logo" class="logo">
    <img src="../Images/doctor-photo.png" alt="Illustration">
  </div>

  <div class="login-right">
    <h1>Forgot Password</h1>
    <p>Enter your NIC and we will send a reset link to your registered E-mail.</p>
	<br>
    <form id="forgotForm">
	  <label for="nic">NIC Number</label>
	  <input type="text" id="nic" name="nic" placeholder="Enter NIC" required/>
	  <br><br>
	  <button type="submit" class="btn">SEND RESET LINK</button>
	</form>
	<br>
    <p class="switch-text"><a href="../HTML/Login.html">BACK TO LOGIN</a></p>
  </div>
</div>

<!-- Success Modal -->
<div id="successModal" class="modal">
  <div class="modal-content">
    <p>If a patient with this NIC exists, a password reset email has been sent.</p>
    <button id="closeModal">OK</button>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('forgotForm');
    const modal = document.getElementById('successModal');
    const closeModalBtn = document.getElementById('closeModal');

    form.addEventListener('submit', function(e) {
        e.preventDefault(); // prevent normal form submission

        const nic = document.getElementById('nic').value;
        const formData = new FormData();
        formData.append('nic', nic);

        fetch('../PHP/PatientForgotPassword.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                modal.style.display = 'flex';
            } else {
                alert(data.message);
            }
        })
        .catch(err => {
            alert('Something went wrong.');
            console.error(err);
        });
    });

    closeModalBtn.addEventListener('click', function() {
        modal.style.display = 'none';
    });
});
</script>


</body>
</html>
