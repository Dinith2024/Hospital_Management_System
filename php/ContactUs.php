<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>CareLink | Contact Us</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../CSS/Contact.css">
<style>
/* Reset & font */
* { margin:0; padding:0; box-sizing:border-box; font-family:'Inter',sans-serif; }
/* Body background only, no flex centering */
html, body {
  height: 110%;
  width: 100%;
  margin: 0;
  font-family: 'Inter', sans-serif;
  background: linear-gradient(135deg,#d3a6df,#f6e8f9);
}

body {
  margin: 0;
  font-family: 'Inter', sans-serif;
  background: linear-gradient(135deg,#d3a6df,#f6e8f9);
}

/* Header and Footer spacing */
#header-placeholder {
  margin-bottom: -40px; /* space between header and card */
}
#footer-placeholder {
  margin-top: -30px; /* space between card and footer */
}

/* Main container for centering the card */
main {
  display: flex;
  justify-content: center;
  align-items: flex-start; /* align to top */
  margin-top: 60px; /* space below header, adjust as needed */
  padding: 0 20px;   /* optional horizontal padding */
}

/* Contact Card */
.contact-card {
  display:flex;
  width:900px;
  max-width:95%;
  background:#fff;
  border-radius:25px;
  box-shadow:0 15px 35px rgba(0,0,0,0.12);
  overflow:hidden;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.contact-card:hover { transform:translateY(-5px); box-shadow:0 20px 45px rgba(0,0,0,0.15); }

/* Left side illustration */
.contact-left {
  flex:1;
  background:#f0e6f9;
  display:flex;
  justify-content:center;
  align-items:center;
  padding:20px;
}
.contact-left img { max-width:100%; max-height:70vh; object-fit:contain; }

/* Right side form */
.contact-right {
  flex:1;
  padding:40px;
}
.contact-right h1 {
  text-align:center;
  color:#58105d;
  font-size:2.2rem;
  margin-bottom:10px;
}
.contact-right p {
  text-align:center;
  color:#58105d;
  margin-bottom:30px;
  font-size:1rem;
}

/* Form */
.form { display:flex; flex-direction:column; gap:20px; }
.input-wrapper { position:relative; display:flex; align-items:center; overflow:visible; }
.input-wrapper svg {
  position:absolute;
  left:14px;
  top:50%;
  transform:translateY(-50%);
  color:#8b5cd6;
  pointer-events:none;
  width:18px;
  height:18px;
}
input,textarea {
  width:100%;
  padding:14px 14px 14px 44px; /* leave space for icon */
  border-radius:15px;
  border:1px solid #c7b3ec;
  background:#fafafa;
  font-size:1rem;
  outline:none;
  transition: all 0.3s ease;
}
input:focus, textarea:focus {
  border-color:#eb5fe7;
  box-shadow:0 0 10px rgba(235,95,231,0.25);
  background:#fff;
}
textarea { resize:none; min-height:120px; }

/* Button */
.btn {
  padding:14px;
  border-radius:15px;
  border:none;
  background:linear-gradient(135deg,#eb5fe7,#8b5cd6);
  color:#fff;
  font-weight:bold;
  cursor:pointer;
  transition: all 0.3s ease;
  display:flex; justify-content:center; align-items:center; gap:10px;
}
.btn:hover { transform:translateY(-3px); box-shadow:0 8px 20px rgba(235,95,231,0.35); }

/* Loader */
.loader-circle {
  width:18px;
  height:18px;
  border:2px solid rgba(255,255,255,0.3);
  border-top-color:#fff;
  border-radius:50%;
  animation:spin 1s linear infinite;
  display:none;
}
@keyframes spin { to { transform: rotate(360deg); } }

/* Responsive */
@media(max-width:768px) { 
  .contact-card { flex-direction:column; } 
  .contact-right{ padding:25px; } 
}
@media(max-width:480px){ 
  .contact-right h1{font-size:1.8rem;} 
  .contact-right p{font-size:0.95rem;} 
  input,textarea{padding-left:38px;} 
}
</style>
</head>
<body>
<div id="header-placeholder"></div>
<br>
  <main class="contact-container">
<div class="contact-card">
  <div class="contact-left">
    <img src="../IMAGES/contactus.png" alt="Contact Illustration">
  </div>
  <div class="contact-right">
    <h1>Get in Touch</h1>
    <p>We'd love to hear from you! Drop us a line and we'll get back to you as soon as possible.</p>
    <form class="form" id="contactForm">
      <div class="input-wrapper">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M20 21V19C20 17.9391 19.5786 16.9217 18.8284 16.1716C18.0783 15.4214 17.0609 15 16 15H8C6.93913 15 5.92172 15.4214 5.17157 16.1716C4.42143 16.9217 4 17.9391 4 19V21M16 7C16 9.20914 14.2091 11 12 11C9.79086 11 8 9.20914 8 7C8 4.79086 9.79086 3 12 3C14.2091 3 16 4.79086 16 7Z"/>
        </svg>
        <input type="text" name="name" placeholder="Full Name" required>
      </div>
      <div class="input-wrapper">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M4 4H20C21.1 4 22 4.9 22 6V18C22 19.1 21.1 20 20 20H4C2.9 20 2 19.1 2 18V6C2 4.9 2.9 4 4 4Z"/>
          <path d="M22 6L12 13L2 6"/>
        </svg>
        <input type="email" name="email" placeholder="Email Address" required>
      </div>
      <div class="input-wrapper">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M9 12L11 14L15 10M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z"/>
        </svg>
        <input type="text" name="subject" placeholder="Subject" required>
      </div>
      <div class="input-wrapper">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M21 15V21H3V15M21 15L12 7L3 15"/>
        </svg>
        <textarea name="message" placeholder="Your Message..." required></textarea>
      </div>
      <button type="submit" class="btn">
        <span>Send Message</span>
        <div class="loader-circle"></div>
      </button>
    </form>
  </div>
</div>
</main>
<!-- Modal stays hidden by default -->
<div id="successModal" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
    <h2>Message Sent!</h2>
    <p>Thank you for contacting us. We'll get back to you shortly.</p>
  </div>
</div>

<!-- Footer -->
<div id="footer-placeholder"></div>

<!-- Scripts: always at the end -->
<script>
  fetch('../HTML/header.php')
    .then(res => res.text())
    .then(data => document.getElementById('header-placeholder').innerHTML = data);

  fetch('../HTML/footer.php')
    .then(res => res.text())
    .then(data => document.getElementById('footer-placeholder').innerHTML = data);
</script>

<script src="../JS/Contact.js"></script>

 
</body>
</html>
