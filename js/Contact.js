document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('contactForm');
  const btn = form.querySelector('.btn');
  const loader = form.querySelector('.loader-circle');
  const modal = document.getElementById('successModal');
  const closeBtn = modal.querySelector('.close');

  form.addEventListener('submit', e => {
    e.preventDefault();
    loader.style.display = 'inline-block';
    btn.disabled = true;

    const formData = new FormData(form);

    fetch('../PHP/Contact.php', { method: 'POST', body: formData })
      .then(res => res.json())
      .then(data => {
        loader.style.display = 'none';
        btn.disabled = false;

        if (data.status === 'success') {
          form.reset();
          modal.style.display = 'flex'; // show modal
        } else {
          alert(data.message || 'Something went wrong');
        }
      })
      .catch(err => {
        loader.style.display = 'none';
        btn.disabled = false;
        console.error(err);
        alert('Something went wrong. Try again.');
      });
	  
	  
  // Close modal
  closeBtn.onclick = () => modal.style.display = 'none';
  window.onclick = e => { if(e.target == modal) modal.style.display = 'none'; }
  });

});
