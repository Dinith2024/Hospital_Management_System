<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CareLink</title>
  <link rel="stylesheet" href="../CSS/header.css">
</head>
<body>
  <!-- Header -->
  <header class="header">
    <div class="nav-container">
      <div class="logo">
        <img src="../IMAGES/logo.png" alt="CareLink Logo" class="logo-img">
      </div>

      <ul class="nav-menu">
        <li><a href="CARELINK.html">HOME</a></li>
        <li><a href="About2.html">ABOUT US</a></li>
        <li class="dropdown">
          <a href="#">SERVICES</a>
          <ul class="dropdown-content">
            <li><a href="PatientDashboard.html">PATIENT</a></li>
            <li><a href="DoctorDash.html">DOCTOR</a></li>
          </ul>
        </li>
        <li><a href="contact.html">CONTACT US</a></li>
      </ul>

      <div class="auth-buttons">
        <a href="login.html" class="login-btn">LOGIN</a>
        <a href="signup.html" class="signup-btn">SIGN UP</a>
      </div>

      <div class="hamburger" onclick="document.querySelector('.nav-menu').classList.toggle('active')">
        <span></span>
        <span></span>
        <span></span>
      </div>
    </div>
  </header>
</body>
</html>
