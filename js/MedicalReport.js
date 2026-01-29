// Global variables
let uploadedFiles = [];
const maxFileSize = 10 * 1024 * 1024; // 10MB
const allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];

// DOM elements
const reportForm = document.getElementById('reportForm');
const fileInput = document.getElementById('fileInput');
const successModal = document.getElementById('successModal');

document.addEventListener('DOMContentLoaded', () => {
    // Set today's date as default
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('testDate').value = today;

    // Initialize file handling and form submit
    initializeFileUpload();
    reportForm.addEventListener('submit', handleFormSubmit);

    // Modal close
    const modalClose = successModal.querySelector('.close');
    modalClose.onclick = () => successModal.style.display = 'none';
    window.onclick = (e) => { if (e.target === successModal) successModal.style.display = 'none'; };
});

// File handling
function initializeFileUpload() {
    fileInput.addEventListener('change', e => handleFiles(e.target.files));
}

function handleFiles(files) {
    Array.from(files).forEach(file => {
        if (!validateFile(file)) return;
        uploadedFiles = [file]; // Only one file at a time
    });
}

function validateFile(file) {
    if (!allowedTypes.includes(file.type)) {
        alert('Only PDF, JPG, PNG allowed.');
        return false;
    }
    if (file.size > maxFileSize) {
        alert('File size must be less than 10MB.');
        return false;
    }
    return true;
}

// Form submission via AJAX
async function handleFormSubmit(e) {
    e.preventDefault();

    const submitBtn = reportForm.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = 'Submitting...';
    submitBtn.disabled = true;

    const formData = new FormData(reportForm);

    // Attach uploaded file if exists
    if (uploadedFiles.length > 0) {
        formData.set('attachment', uploadedFiles[0]);
    }

    try {
        const response = await fetch('../PHP/AddMedicalRecord.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.success) {
            successModal.style.display = 'flex';
            reportForm.reset();
            uploadedFiles = [];
        } else {
            alert(result.message || 'Error submitting report.');
        }
    } catch (error) {
        console.error(error);
        alert('Unexpected error occurred.');
    } finally {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }
}
