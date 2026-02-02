document.addEventListener("DOMContentLoaded", () => {
  // --- TAB SWITCHING ---
  const tabButtons = document.querySelectorAll(".tab-btn");
  const forms = document.querySelectorAll(".form");

  tabButtons.forEach((btn) => {
    btn.addEventListener("click", () => {
      tabButtons.forEach((b) => b.classList.remove("active"));
      btn.classList.add("active");

      forms.forEach((form) => form.classList.add("hidden"));

      const target = document.getElementById(btn.dataset.tab);
      if (target) target.classList.remove("hidden");
    });
  });

  // --- FORM VALIDATION ---
  document.querySelectorAll("form").forEach((form) => {
    form.addEventListener("submit", (e) => {
      const password = form.querySelector('input[name="password"]');
      const confirmPassword = form.querySelector('input[name="confirm_password"]');
      const nic = form.querySelector('input[name="nic_number"]');
      const phone = form.querySelector('input[name="phone_number"]');

      // Password strength: 8+ chars, upper, lower, number, symbol
      const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;

      if (password && !passwordRegex.test(password.value)) {
        e.preventDefault();
        alert("Password must be at least 8 characters and include uppercase, lowercase, number, and symbol.");
        password.focus();
        return;
      }

      // Confirm password match
      if (password && confirmPassword && password.value !== confirmPassword.value) {
        e.preventDefault();
        alert("Passwords do not match!");
        confirmPassword.focus();
        return;
      }

      // NIC validation: pre-2016 9 digits + V or post-2016 12 digits
      const nicRegex = /^(\d{9}[vV]|\d{12})$/;
      if (nic && !nicRegex.test(nic.value)) {
        e.preventDefault();
        alert("Enter valid NIC: 9 digits + V (pre-2016) or 12 digits (post-2016).");
        nic.focus();
        return;
      }

      // Phone number validation: 10 digits
      const phoneRegex = /^\d{10}$/;
      if (phone && !phoneRegex.test(phone.value)) {
        e.preventDefault();
        alert("Phone number must be exactly 10 digits.");
        phone.focus();
        return;
      }
    });
  });
});

const modal = document.getElementById("errorModal");
document.getElementById("errorMsg").innerText = data.message;
modal.style.display = "block";

