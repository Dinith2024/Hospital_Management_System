// Tab switching
document.querySelectorAll('.tab-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById(btn.dataset.tab).classList.add('active');
  });
});

// Profile
const editBtn = document.getElementById('editProfileBtn');
const cancelBtn = document.getElementById('cancelEditBtn');
const profileForm = document.getElementById('profileForm');
const profileView = document.getElementById('profileView');
const profilePreviewForm = document.getElementById('profilePreviewForm');
const profileInputForm = document.getElementById('profileImageForm');
const profilePreviewView = document.getElementById('profilePreviewView');

editBtn.addEventListener('click', () => {
  profileView.style.display = 'none';
  profileForm.style.display = 'block';
});

cancelBtn.addEventListener('click', () => {
  profileForm.style.display = 'none';
  profileView.style.display = 'block';
});

// Choose image
const profileContainer = document.querySelector('.profile-img-container.profile-hover');
profileContainer.addEventListener('click', () => {
  profileInputForm.click();
});

// Preview new image
profileInputForm.addEventListener('change', function () {
  const file = this.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = function (e) {
      if(profilePreviewForm.tagName === 'IMG') {
        profilePreviewForm.src = e.target.result;
      } else {
        profilePreviewForm.outerHTML = `<img src="${e.target.result}" class="profile-img" id="profilePreviewForm">`;
      }
    }
    reader.readAsDataURL(file);
  }
});

const startTimeInput = document.getElementById('start_time');
const endTimeInput = document.getElementById('end_time');
const slotDurationInput = document.getElementById('slot_duration');
const numSlotsInput = document.getElementById('num_slots');

function calculateSlots() {
  const startTime = startTimeInput.value;
  const endTime = endTimeInput.value;
  const duration = parseInt(slotDurationInput.value, 10);

  // ensure hidden field exists
  const hiddenNumSlots = document.getElementById('num_slots_hidden');

  if (startTime && endTime && Number.isInteger(duration) && duration > 0) {
    const [sh, sm] = startTime.split(':').map(x => parseInt(x, 10));
    const [eh, em] = endTime.split(':').map(x => parseInt(x, 10));

    let startMinutes = sh * 60 + sm;
    let endMinutes = eh * 60 + em;

    // If end is same or earlier than start, assume the schedule crosses midnight -> treat end as next day
    if (endMinutes <= startMinutes) {
      endMinutes += 24 * 60; // add 24 hours in minutes
    }

    const diff = endMinutes - startMinutes; // total minutes available
    let slots = Math.floor(diff / duration);

    // Ensure at least 1 slot
    if (slots < 1) slots = 1;

    numSlotsInput.value = slots;
    if (hiddenNumSlots) hiddenNumSlots.value = slots;
  } else {
    // fallback
    numSlotsInput.value = 1;
    if (hiddenNumSlots) hiddenNumSlots.value = 1;
  }
}


// Trigger calculation on input change
startTimeInput.addEventListener('change', calculateSlots);
endTimeInput.addEventListener('change', calculateSlots);
slotDurationInput.addEventListener('input', calculateSlots);

// DoctorDashboardScript.js

document.querySelectorAll('.delete-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const scheduleId = btn.dataset.id;
        if (confirm("Are you sure you want to delete this schedule?")) {
            fetch(`../HTML/DoctorSchedule.php?delete_id=${scheduleId}`, { method: 'GET' })
				.then(res => res.text())
				.then(data => {
                    if(data === 'success'){
                        alert('Schedule deleted!');
                        location.reload();
                    } else {
                        alert('Error deleting schedule.');
                    }
                });
        }
    });
});

document.querySelectorAll('.edit-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const scheduleRow = btn.closest('tr');
        const scheduleId = btn.dataset.id;
        const day = scheduleRow.children[0].innerText;
        const startTime = scheduleRow.children[1].innerText;
        const endTime = scheduleRow.children[2].innerText;
        const duration = scheduleRow.children[3].innerText;

        // Fill the schedule form with existing values
        document.getElementById('day').value = day;
        document.getElementById('start_time').value = startTime;
        document.getElementById('end_time').value = endTime;
        document.getElementById('slot_duration').value = duration;
        document.getElementById('num_slots_hidden').value = scheduleRow.children[4].innerText;

        // Change form action to update
        const form = document.getElementById('scheduleForm');
        form.action = `../HTML/DoctorSchedule.php?id=${scheduleId}`;

        // Scroll to form or highlight
        form.scrollIntoView({ behavior: 'smooth' });
    });
});

const scheduleForm = document.getElementById('scheduleForm');
scheduleForm.addEventListener('submit', function(e){
    e.preventDefault(); // stop default form submission

    const formData = new FormData(scheduleForm);

    fetch('../HTML/DoctorSchedule.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.text())
    .then(data => {
        if(data.includes('success')){
            // show modal
            const modal = document.getElementById("scheduleModal");
            modal.style.display = "block";
            // optionally reload schedules table via JS
        } else {
            alert(data); // show error
        }
    })
    .catch(err => console.error(err));
});
