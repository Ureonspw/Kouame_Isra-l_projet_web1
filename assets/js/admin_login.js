document.getElementById('loginForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const submitBtn = document.getElementById('submitBtn');
    const loadingSpinner = submitBtn.querySelector('.loading');
    const submitText = submitBtn.querySelector('span');
    
    // Show loading state
    submitBtn.disabled = true;
    submitText.style.display = 'none';
    loadingSpinner.style.display = 'block';

    // Get form data
    const formData = new FormData(this);

    // Send login request
    fetch('process_admin_login.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Succès !',
                text: data.message,
                confirmButtonColor: '#F47721'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = data.redirect;
                }
            });
        } else {
            throw new Error(data.message);
        }
    })
    .catch(error => {
        Swal.fire({
            icon: 'error',
            title: 'Erreur',
            text: error.message || 'Une erreur est survenue lors de la connexion',
            confirmButtonColor: '#F47721'
        });
    })
    .finally(() => {
        // Reset button state
        submitBtn.disabled = false;
        submitText.style.display = 'block';
        loadingSpinner.style.display = 'none';
    });
});
