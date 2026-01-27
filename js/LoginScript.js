document.addEventListener("DOMContentLoaded", () => {
  const tabs = document.querySelectorAll(".tab-btn");
  const forms = document.querySelectorAll(".form");
  const modal = document.getElementById("errorModal");
  const errorMsg = document.getElementById("errorMsg");
  const closeBtn = document.querySelector(".close");
  const modalOk = document.getElementById("modalOk");

  // --- TAB SWITCH ---
  tabs.forEach(tab => {
    tab.addEventListener("click", () => {
      tabs.forEach(t => t.classList.remove("active"));
      forms.forEach(f => f.classList.add("hidden"));
      forms.forEach(f => f.classList.remove("active"));

      tab.classList.add("active");
      const tabForm = document.getElementById(tab.dataset.tab);
      tabForm.classList.remove("hidden");
      tabForm.classList.add("active");
    });
  });

  // --- FORM SUBMISSION ---
  forms.forEach(form => {
    form.addEventListener("submit", async (e) => {
      e.preventDefault(); // prevent normal submit
      const formData = new FormData(form);

      try {
        const res = await fetch(form.action, { method: "POST", body: formData });
        const data = await res.json();

        modal.style.display = "flex";
        errorMsg.style.color = data.success ? '#1a8917' : '#b00020';
        errorMsg.innerText = data.message;

        const redirectIfSuccess = () => {
          modal.style.display = "none";
          if (data.success && data.redirect) {
            console.log("Redirecting to:", data.redirect);
            window.location.href = data.redirect;
          }
        };

        // attach only once
        modalOk.onclick = redirectIfSuccess;
        closeBtn.onclick = redirectIfSuccess;
        window.onclick = (event) => { if (event.target === modal) redirectIfSuccess(); };

      } catch (err) {
        console.error(err);
        errorMsg.style.color = '#b00020';
        errorMsg.innerText = "Something went wrong. Try again.";
        modal.style.display = "flex";
        modalOk.onclick = closeBtn.onclick = () => { modal.style.display = "none"; };
      }
    });
  });
});
